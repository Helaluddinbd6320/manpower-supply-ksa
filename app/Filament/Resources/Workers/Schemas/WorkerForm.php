<?php

namespace App\Filament\Resources\Workers\Schemas;

use App\Enums\DocumentType;
use App\Enums\IqamaStatus;
use App\Enums\LanguageProficiency;
use App\Enums\LocationType;
use App\Enums\Nationality;
use App\Models\Worker;
use App\Models\Agent;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class WorkerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Worker Details')
                    ->columnSpanFull()
                    ->tabs([

                        // ---------------- BASIC INFO (ESSENTIAL) ----------------
                        Tab::make('Basic Info')
                            ->schema([
                                Section::make('Essential Details')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Full Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Select::make('sourcing_agent_id')
                                            ->label('Sourced via Agent')
                                            ->relationship('sourcingAgent', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->placeholder('Direct entry (no agent)')
                                            ->helperText('If this worker was referred by an agent, select them here.'),

                                        Select::make('gender')
                                            ->label('Gender')
                                            ->options([
                                                'male' => 'Male',
                                                'female' => 'Female',
                                            ])
                                            ->required(),

                                        Select::make('nationality')
                                            ->label('Nationality / Country')
                                            ->options(Nationality::class)
                                            ->default(Nationality::Bangladesh)
                                            ->searchable()
                                            ->native(false)
                                            ->required(),

                                        TextInput::make('mobile_number')
                                            ->label('Mobile Number')
                                            ->tel()
                                            ->required()
                                            ->maxLength(20)
                                            ->live(onBlur: true)
                                            ->helperText('A warning will show if this number already exists.'),

                                        Placeholder::make('mobile_duplicate_warning')
                                            ->hiddenLabel()
                                            ->visible(fn(Get $get, ?Worker $record) => self::findDuplicateMobile($get('mobile_number'), $record) !== null)
                                            ->content(function (Get $get, ?Worker $record) {
                                                $match = self::findDuplicateMobile($get('mobile_number'), $record);

                                                return $match
                                                    ? new HtmlString(
                                                        '<div class="text-sm font-medium text-warning-600">⚠️ A worker with this mobile number already exists: '
                                                            . e($match->worker_id) . ' — ' . e($match->name)
                                                            . ' (warning only, not blocked)</div>'
                                                    )
                                                    : null;
                                            }),

                                        CheckboxList::make('jobCategories')
                                            ->label('Job Categories')
                                            ->relationship('jobCategories', 'name')
                                            ->columns(3)
                                            ->searchable()
                                            ->required()
                                            ->columnSpanFull(),

                                        Select::make('employment_status')
                                            ->label('Employment Status')
                                            ->options([
                                                'free_available' => 'Free / Available',
                                                'currently_working' => 'Currently Working',
                                            ])
                                            ->required()
                                            ->live()
                                            ->columnSpanFull(),

                                        Placeholder::make('confirmed_job_order_info')
                                            ->hiddenLabel()
                                            ->visible(fn(?Worker $record) => $record && $record->confirmedJobOrder() !== null)
                                            ->content(function (?Worker $record) {
                                                $order = $record?->confirmedJobOrder();

                                                if (! $order) {
                                                    return null;
                                                }

                                                return new HtmlString(
                                                    '<div class="text-sm font-medium text-success-600">✅ Confirmed for: '
                                                        . e($order->company_name) . ' (' . e($order->jobCategory->name) . ')</div>'
                                                );
                                            })
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Photo & Iqama Photo')
                                    ->columns(2)
                                    ->schema([
                                        Repeater::make('photo_document')
                                            ->relationship(
                                                'documents',
                                                modifyQueryUsing: fn(Builder $query) => $query->where('document_type', DocumentType::Photo->value),
                                            )
                                            ->label('Worker Photo')
                                            ->default([])
                                            ->maxItems(1)
                                            ->addActionLabel('Upload Photo')
                                            ->reorderable(false)
                                            ->collapsible(false)
                                            ->simple(
                                                FileUpload::make('file_path')
                                                    ->disk('r2')
                                                    ->directory(function ($livewire) {
                                                        $worker = $livewire->getRecord();
                                                        $workerId = $worker?->worker_id ?? 'temp';

                                                        return "workers/{$workerId}";
                                                    })
                                                    ->visibility('private')
                                                    ->image()
                                                    ->maxSize(5120)
                                                    ->required(),
                                            )
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                if (empty($data['file_path'])) {
                                                    Notification::make()
                                                        ->danger()
                                                        ->title('Photo upload incomplete')
                                                        ->body('Please wait for the photo upload to finish (thumbnail should appear) before saving, then try again.')
                                                        ->send();

                                                    throw new Halt();
                                                }

                                                $data['document_type'] = DocumentType::Photo->value;
                                                $data['uploaded_by'] = Auth::id();

                                                $filePath = is_array($data['file_path']) ? reset($data['file_path']) : $data['file_path'];
                                                $data['original_filename'] = basename($filePath);
                                                $data['mime_type'] = Storage::disk('r2')->mimeType($filePath);
                                                $data['file_size'] = Storage::disk('r2')->size($filePath);

                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                                if (empty($data['file_path'])) {
                                                    Notification::make()
                                                        ->danger()
                                                        ->title('Photo upload incomplete')
                                                        ->body('Please wait for the photo upload to finish (thumbnail should appear) before saving, then try again.')
                                                        ->send();

                                                    throw new Halt();
                                                }

                                                $data['document_type'] = DocumentType::Photo->value;

                                                $filePath = is_array($data['file_path']) ? reset($data['file_path']) : $data['file_path'];
                                                $data['original_filename'] = basename($filePath);
                                                $data['mime_type'] = Storage::disk('r2')->mimeType($filePath);
                                                $data['file_size'] = Storage::disk('r2')->size($filePath);

                                                return $data;
                                            }),

                                        Repeater::make('iqama_document')
                                            ->relationship(
                                                'documents',
                                                modifyQueryUsing: fn(Builder $query) => $query->where('document_type', DocumentType::Iqama->value),
                                            )
                                            ->label('Iqama Photo')
                                            ->default([])
                                            ->maxItems(1)
                                            ->addActionLabel('Upload Iqama Photo')
                                            ->reorderable(false)
                                            ->collapsible(false)
                                            ->simple(
                                                FileUpload::make('file_path')
                                                    ->disk('r2')
                                                    ->directory(function ($livewire) {
                                                        $worker = $livewire->getRecord();
                                                        $workerId = $worker?->worker_id ?? 'temp';

                                                        return "workers/{$workerId}";
                                                    })
                                                    ->visibility('private')
                                                    ->image()
                                                    ->maxSize(5120)
                                                    ->required(),
                                            )
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                if (empty($data['file_path'])) {
                                                    Notification::make()
                                                        ->danger()
                                                        ->title('Iqama photo upload incomplete')
                                                        ->body('Please wait for the iqama photo upload to finish (thumbnail should appear) before saving, then try again.')
                                                        ->send();

                                                    throw new Halt();
                                                }

                                                $data['document_type'] = DocumentType::Iqama->value;
                                                $data['uploaded_by'] = Auth::id();

                                                $filePath = is_array($data['file_path']) ? reset($data['file_path']) : $data['file_path'];
                                                $data['original_filename'] = basename($filePath);
                                                $data['mime_type'] = Storage::disk('r2')->mimeType($filePath);
                                                $data['file_size'] = Storage::disk('r2')->size($filePath);

                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                                if (empty($data['file_path'])) {
                                                    Notification::make()
                                                        ->danger()
                                                        ->title('Iqama photo upload incomplete')
                                                        ->body('Please wait for the iqama photo upload to finish (thumbnail should appear) before saving, then try again.')
                                                        ->send();

                                                    throw new Halt();
                                                }

                                                $data['document_type'] = DocumentType::Iqama->value;

                                                $filePath = is_array($data['file_path']) ? reset($data['file_path']) : $data['file_path'];
                                                $data['original_filename'] = basename($filePath);
                                                $data['mime_type'] = Storage::disk('r2')->mimeType($filePath);
                                                $data['file_size'] = Storage::disk('r2')->size($filePath);

                                                return $data;
                                            }),
                                    ]),
                            ]),

                        // ---------------- ADDITIONAL DETAILS (OPTIONAL) ----------------
                        Tab::make('Additional Details')
                            ->schema([
                                Section::make('Passport & Personal Details')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('passport_number')
                                            ->label('Passport Number')
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(50)
                                            ->live(onBlur: true)
                                            ->helperText('Duplicate passport numbers are not allowed.'),

                                        Placeholder::make('passport_duplicate_warning')
                                            ->hiddenLabel()
                                            ->visible(fn(Get $get, ?Worker $record) => self::findDuplicatePassport($get('passport_number'), $record) !== null)
                                            ->content(function (Get $get, ?Worker $record) {
                                                $match = self::findDuplicatePassport($get('passport_number'), $record);

                                                return $match
                                                    ? new HtmlString(
                                                        '<div class="text-sm font-medium text-danger-600">⚠️ This passport number already exists for worker '
                                                            . e($match->worker_id) . ' — ' . e($match->name) . '</div>'
                                                    )
                                                    : null;
                                            }),

                                        DatePicker::make('passport_issue_date')
                                            ->label('Passport Issue Date'),

                                        DatePicker::make('passport_expiry_date')
                                            ->label('Passport Expiry Date')
                                            ->after('passport_issue_date'),

                                        DatePicker::make('date_of_birth')
                                            ->label('Date of Birth')
                                            ->maxDate(now()->subYears(18)),

                                        TextInput::make('religion')
                                            ->label('Religion')
                                            ->maxLength(100),

                                        Select::make('marital_status')
                                            ->label('Marital Status')
                                            ->options([
                                                'single' => 'Single',
                                                'married' => 'Married',
                                                'divorced' => 'Divorced',
                                                'widowed' => 'Widowed',
                                            ]),
                                    ]),

                                Section::make('Contact & Address')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('emergency_contact_number')
                                            ->label('Emergency Contact Number')
                                            ->tel()
                                            ->maxLength(20),

                                        TextInput::make('district')
                                            ->label('District')
                                            ->maxLength(100),

                                        TextInput::make('upazila')
                                            ->label('Upazila')
                                            ->maxLength(100),
                                    ]),
                            ]),

                        // ---------------- PROFESSIONAL INFO (OPTIONAL) ----------------
                        Tab::make('Professional Info')
                            ->schema([
                                Section::make('Experience & Skills')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('experience_years')
                                            ->label('Years of Experience')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(60),

                                        Select::make('driving_license_type')
                                            ->label('Driving License Type')
                                            ->options([
                                                'none' => 'None',
                                                'light' => 'Light Vehicle',
                                                'heavy' => 'Heavy Vehicle',
                                            ])
                                            ->default('none'),

                                        Select::make('arabic_proficiency')
                                            ->label('Arabic Proficiency')
                                            ->options(LanguageProficiency::class)
                                            ->default(LanguageProficiency::None),

                                        Select::make('english_proficiency')
                                            ->label('English Proficiency')
                                            ->options(LanguageProficiency::class)
                                            ->default(LanguageProficiency::None),

                                        TextInput::make('education_qualification')
                                            ->label('Educational Qualification')
                                            ->maxLength(255),

                                        TextInput::make('trade_test_certificate')
                                            ->label('Trade Test Certificate')
                                            ->maxLength(255),

                                        Textarea::make('experience_description')
                                            ->label('Experience Description')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ---------------- LOCATION STATUS (OPTIONAL) ----------------
                        Tab::make('Location Status')
                            ->schema([
                                Select::make('location_type')
                                    ->label('Location Type')
                                    ->options(LocationType::class)
                                    ->live()
                                    ->columnSpanFull(),

                                Section::make('Saudi Arabia Details')
                                    ->columns(2)
                                    ->visible(function (Get $get) {
                                        $value = $get('location_type');

                                        return $value instanceof LocationType
                                            ? $value === LocationType::InSaudiArabia
                                            : $value === LocationType::InSaudiArabia->value;
                                    })
                                    ->schema([
                                        Select::make('iqama_status')
                                            ->label('Iqama Status')
                                            ->options(IqamaStatus::class)
                                            ->live()
                                            ->helperText('Company-facing filter — used to find only valid, non-huroob workers.')
                                            ->columnSpanFull(),

                                        TextInput::make('iqama_number')
                                            ->label('Iqama Number')
                                            ->maxLength(50)
                                            ->visible(function (Get $get) {
                                                $value = $get('iqama_status');

                                                return $value instanceof IqamaStatus
                                                    ? $value !== IqamaStatus::NoIqamaBorderNumber
                                                    : $value !== IqamaStatus::NoIqamaBorderNumber->value;
                                            }),

                                        TextInput::make('border_number')
                                            ->label('Border Number (Rahil No.)')
                                            ->maxLength(50)
                                            ->visible(function (Get $get) {
                                                $value = $get('iqama_status');

                                                return $value instanceof IqamaStatus
                                                    ? $value === IqamaStatus::NoIqamaBorderNumber
                                                    : $value === IqamaStatus::NoIqamaBorderNumber->value;
                                            })
                                            ->required(function (Get $get) {
                                                $value = $get('iqama_status');

                                                return $value instanceof IqamaStatus
                                                    ? $value === IqamaStatus::NoIqamaBorderNumber
                                                    : $value === IqamaStatus::NoIqamaBorderNumber->value;
                                            }),

                                        DatePicker::make('iqama_expiry_date')
                                            ->label('Iqama Expiry Date')
                                            ->visible(function (Get $get) {
                                                $value = $get('iqama_status');

                                                return $value instanceof IqamaStatus
                                                    ? $value !== IqamaStatus::NoIqamaBorderNumber
                                                    : $value !== IqamaStatus::NoIqamaBorderNumber->value;
                                            }),

                                        TextInput::make('iqama_occupation')
                                            ->label('Profession (as per Iqama)')
                                            ->maxLength(150),

                                        TextInput::make('current_city')
                                            ->label('Current City')
                                            ->maxLength(150),

                                        Toggle::make('kafala_transfer_interested')
                                            ->label('Interested in Kafala Transfer')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ---------------- EMPLOYMENT STATUS DETAILS (OPTIONAL) ----------------
                        Tab::make('Employment Status')
                            ->schema([
                                Section::make('Current Job Details')
                                    ->columns(2)
                                    ->visible(fn(Get $get) => $get('employment_status') === 'currently_working')
                                    ->schema([
                                        DatePicker::make('contract_end_date')
                                            ->label('Contract End Date'),

                                        TextInput::make('notice_period')
                                            ->label('Notice Period')
                                            ->maxLength(100),

                                        Textarea::make('change_reason')
                                            ->label('Reason for Change')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Availability')
                                    ->columns(2)
                                    ->visible(fn(Get $get) => $get('employment_status') === 'free_available')
                                    ->schema([
                                        DatePicker::make('available_from_date')
                                            ->label('Available From'),

                                        TextInput::make('expected_salary')
                                            ->label('Expected Salary')
                                            ->numeric()
                                            ->prefix('SAR'),
                                    ]),
                            ]),

                        // ---------------- DOCUMENTS (CERTIFICATES) ----------------
                        Tab::make('Documents')
                            ->schema([
                                Repeater::make('certificates')
                                    ->relationship('certificates')
                                    ->label('Certificates')
                                    ->default([])
                                    ->schema([
                                        TextInput::make('label')
                                            ->label('Certificate Title')
                                            ->placeholder('e.g. Trade Test Certificate, Welding License')
                                            ->maxLength(255)
                                            ->required(),

                                        FileUpload::make('file_path')
                                            ->label('Certificate File')
                                            ->disk('r2')
                                            ->directory(function (Get $get, $livewire) {
                                                $worker = $livewire->getRecord();
                                                $workerId = $worker?->worker_id ?? 'temp';

                                                return "workers/{$workerId}/certificates";
                                            })
                                            ->visibility('private')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable()
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Add Certificate')
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->collapsed(fn(?array $state): bool => filled($state))
                                    ->itemLabel(fn(array $state): ?string => $state['label'] ?? 'New Certificate')
                                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                        if (empty($data['file_path'])) {
                                            Notification::make()
                                                ->danger()
                                                ->title('Certificate file missing')
                                                ->body('One of the certificate entries has no file attached (label: "' . ($data['label'] ?? 'untitled') . '"). Please wait for the upload to finish (you should see a thumbnail/filename) before saving, then try again.')
                                                ->send();

                                            throw new Halt();
                                        }

                                        $data['document_type'] = DocumentType::Certificate->value;
                                        $data['uploaded_by'] = Auth::id();

                                        $filePath = is_array($data['file_path']) ? reset($data['file_path']) : $data['file_path'];
                                        $data['original_filename'] = basename($filePath);
                                        $data['mime_type'] = Storage::disk('r2')->mimeType($filePath);
                                        $data['file_size'] = Storage::disk('r2')->size($filePath);

                                        return $data;
                                    })
                                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                        if (empty($data['file_path'])) {
                                            Notification::make()
                                                ->danger()
                                                ->title('Certificate file missing')
                                                ->body('One of the certificate entries has no file attached (label: "' . ($data['label'] ?? 'untitled') . '"). Please wait for the upload to finish (you should see a thumbnail/filename) before saving, then try again.')
                                                ->send();

                                            throw new Halt();
                                        }

                                        $data['document_type'] = DocumentType::Certificate->value;

                                        $filePath = is_array($data['file_path']) ? reset($data['file_path']) : $data['file_path'];
                                        $data['original_filename'] = basename($filePath);
                                        $data['mime_type'] = Storage::disk('r2')->mimeType($filePath);
                                        $data['file_size'] = Storage::disk('r2')->size($filePath);

                                        return $data;
                                    })
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    protected static function findDuplicatePassport(?string $passportNumber, ?Worker $record): ?Worker
    {
        if (blank($passportNumber)) {
            return null;
        }

        return Worker::query()
            ->where('passport_number', $passportNumber)
            ->when($record, fn($query) => $query->where('id', '!=', $record->id))
            ->first();
    }

    protected static function findDuplicateMobile(?string $mobileNumber, ?Worker $record): ?Worker
    {
        if (blank($mobileNumber)) {
            return null;
        }

        return Worker::query()
            ->where('mobile_number', $mobileNumber)
            ->when($record, fn($query) => $query->where('id', '!=', $record->id))
            ->first();
    }
}
