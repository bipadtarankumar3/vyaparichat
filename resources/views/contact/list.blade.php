@php
/**
* Component : Contact
* Controller : ContactController
* File : contact.list.blade.php
* ----------------------------------------------------------------------------- */
$currentGroup = $groupUid ? $vendorContactGroups->where('_uid', $groupUid)->first() : null;
@endphp
@extends('layouts.app', ['title' => __tr('Contacts')])
@section('content')
@php
$groupDescription = $groupUid ? $currentGroup->description : '';
@endphp
@include('users.partials.header', [
    'title' => $groupUid ? __tr('__groupName__ group contacts', [
        '__groupName__' => $currentGroup->title
    ]) : __tr('Contacts'),
    'description' => $groupDescription,
    'class' => 'col-lg-7'
])
<div class="container-fluid mt-3">
    <div class="row">
        {{-- import contacts --}}
        <style>
            #lwImportContactDialog .modal-dialog {
                max-width: 1140px !important;
                width: 92% !important;
            }
        </style>
        <x-lw.modal id="lwImportContactDialog" :header="__tr('Import Contacts')" modal-dialog-class="modal-xl" :hasForm="true">
            <!-- Step 1: Upload Excel File -->
            <div id="lwImportStep1" class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded border">
                    <div>
                        <h4 class="mb-1 text-dark font-weight-bold"><i class="fas fa-file-excel text-success mr-2"></i>{{ __tr('Dummy / Sample Excel Template') }}</h4>
                        <p class="mb-0 text-muted small">{{ __tr('Download sample template with columns: First Name, Second Name, Phone Number, and Email.') }}</p>
                    </div>
                    <div>
                        <a href="{{ route('vendor.contact.dummy_template') }}" class="btn btn-success btn-sm font-weight-bold shadow-sm">
                            <i class="fas fa-download mr-1"></i> {{ __tr('Download Dummy Excel') }}
                        </a>
                    </div>
                </div>

                <div class="alert alert-info py-2 small">
                    <i class="fas fa-info-circle mr-1"></i> {{ __tr('You can import excel file with new contacts or existing updated. Mobile number must include country code without prefixing 0 or +.') }}
                </div>

                <div class="form-group my-3">
                    <input id="lwImportDocumentFilepond" type="file" data-allow-revert="true"
                        data-label-idle="{{ __tr('Drag & Drop or Browse XLSX File') }}" class="lw-file-uploader"
                        data-instant-upload="true"
                        data-action="<?= route('media.upload_temp_media', 'vendor_contact_import') ?>"
                        data-file-input-element="#lwImportDocument" data-allowed-media='{{ getMediaRestriction('vendor_contact_import') }}'>
                    <input id="lwImportDocument" type="hidden" value="" name="document_name" />
                </div>

                <div class="text-right mt-4 border-top pt-3">
                    <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">{{ __tr('Close') }}</button>
                    <button type="button" id="lwBtnGoToPreview" class="btn btn-primary" disabled>
                        <i class="fas fa-table mr-1"></i> {!! __tr('Preview & Select Contacts') !!}
                    </button>
                </div>
            </div>

            <!-- Step 2: Preview & Select Table -->
            <div id="lwImportStep2" class="p-4" style="display:none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <button type="button" id="lwBtnBackToUpload" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> {{ __tr('Upload Different File') }}
                        </button>
                    </div>
                    <div>
                        <span class="badge badge-info px-2 py-1 mr-1" id="lwPreviewBadgeTotal">0 Found</span>
                        <span class="badge badge-success px-2 py-1 mr-1" id="lwPreviewBadgeSelected">0 Selected</span>
                        <span class="badge badge-warning px-2 py-1" id="lwPreviewBadgeInvalid" style="display:none;">0 Invalid</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="lwPreviewSelectAll" checked>
                        <label class="custom-control-label font-weight-bold" for="lwPreviewSelectAll">{{ __tr('Select All Valid') }}</label>
                    </div>
                    <div>
                        <input type="text" id="lwPreviewSearchInput" class="form-control form-control-sm" placeholder="{{ __tr('Search in preview...') }}" style="width: 220px;">
                    </div>
                </div>

                <div class="table-responsive border rounded" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-hover table-striped mb-0 text-center" id="lwImportPreviewTable">
                        <thead class="thead-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th style="width: 40px;"></th>
                                <th style="width: 50px;">#</th>
                                <th class="text-left">{{ __tr('First Name') }}</th>
                                <th class="text-left">{{ __tr('Second Name') }}</th>
                                <th>{{ __tr('Phone Number') }}</th>
                                <th class="text-left">{{ __tr('Email') }}</th>
                                <th>{{ __tr('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody id="lwImportPreviewTableBody">
                        </tbody>
                    </table>
                </div>

                <div class="text-right mt-4 border-top pt-3">
                    <button type="button" id="lwBtnCancelPreviewStep" class="btn btn-secondary mr-2">{{ __tr('Back') }}</button>
                    <button type="button" id="lwBtnConfirmImport" class="btn btn-success font-weight-bold">
                        <i class="fas fa-check mr-1"></i> <span id="lwBtnConfirmImportText">{{ __tr('Import Selected Contacts') }}</span>
                    </button>
                </div>
            </div>
        </x-lw.modal>
        {{-- /import contacts --}}
        {{-- export contacts --}}
        <x-lw.modal id="lwExportDialog" :header="__tr('Export Contacts')" :hasForm="true">
            <div class="lw-form-modal-body p-3">
                <form id="lwExportContactsForm" method="GET" action="{{ route('vendor.contact.write.export') }}">
                    <input type="hidden" name="exportType" value="data">
                    <input type="hidden" name="from_date" id="lwExportFromDate" value="">
                    <input type="hidden" name="to_date" id="lwExportToDate" value="">

                    <div class="card border-0 shadow-sm mb-3" style="background-color: #f8fafc; border-radius: 8px;">
                        <div class="card-body p-3">
                            <h6 class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-filter text-primary mr-1"></i> {{ __tr('Select Contacts to Export') }}
                            </h6>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="lwExportTabLiveChat" name="contact_tab" value="live_chat" class="custom-control-input lw-export-tab-radio">
                                <label class="custom-control-label font-weight-bold" for="lwExportTabLiveChat">
                                    <i class="fab fa-whatsapp text-success mr-1"></i> {{ __tr('Live Chat Contacts') }}
                                </label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="lwExportTabImported" name="contact_tab" value="imported" class="custom-control-input lw-export-tab-radio">
                                <label class="custom-control-label font-weight-bold" for="lwExportTabImported">
                                    <i class="fas fa-file-excel text-primary mr-1"></i> {{ __tr('Imported Contacts') }}
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="lwExportTabAll" name="contact_tab" value="all" class="custom-control-input lw-export-tab-radio">
                                <label class="custom-control-label font-weight-bold" for="lwExportTabAll">
                                    <i class="fas fa-users text-muted mr-1"></i> {{ __tr('All Contacts') }}
                                </label>
                            </div>

                            <div id="lwExportDateInfo" class="mt-3 pt-2 border-top small text-muted">
                                <i class="fas fa-calendar-alt mr-1"></i> <span id="lwExportDateText">{{ __tr('Date Range: All Dates') }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block shadow-sm font-weight-bold py-2 mb-3">
                        <i class="fas fa-file-download mr-1"></i> {{ __('Export Excel File with Data') }}
                    </button>
                </form>

                <hr class="my-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="font-weight-bold mb-0 text-dark">{{ __tr('Blank Excel Template') }}</h6>
                        <small class="text-muted">{{ __tr('Export empty template for importing new contacts') }}</small>
                    </div>
                    <a href="{{ route('vendor.contact.write.export') }}" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-file-excel mr-1"></i> {{ __('Export Blank Template') }}
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
            </div>
        </x-lw.modal>
        {{-- /export contacts --}}
        <!-- Add New Contact Modal -->
        <x-lw.modal id="lwAddNewContact" :header="__tr('Add New Contact')" :hasForm="true"
            data-pre-callback="appFuncs.clearContainer">
            <!--  Add New Contact Form -->
            <x-lw.form id="lwAddNewContactForm" :action="route('vendor.contact.write.create')"
                :data-callback-params="['modalId' => '#lwAddNewContact', 'datatableId' => '#lwContactList']"
                data-callback="appFuncs.modelSuccessCallback">
                <!-- form body -->
                <div class="lw-form-modal-body">
                    <!-- form fields form fields -->
                    <!-- First_Name -->
                    <x-lw.input-field type="text" id="lwFirstNameField" data-form-group-class=""
                        :label="__tr('First Name')" name="first_name"  />
                    <!-- /First_Name -->
                    <!-- Last_Name -->
                    <x-lw.input-field type="text" id="lwLastNameField" data-form-group-class=""
                        :label="__tr('Last Name')" name="last_name"  />
                    <!-- /Last_Name -->
                    <!-- Country -->
                    <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwCountryField"
                        data-form-group-class="" data-selected=" " :label="__tr('Country')" name="country"
                        >
                        <x-slot name="selectOptions">
                            <option value="">{{ __tr('Country') }}</option>
                            @foreach(getCountryPhoneCodes() as $getCountryCode)
                            <option value="{{ $getCountryCode['_id'] }}">{{ $getCountryCode['name'] }}</option>
                            @endforeach
                        </x-slot>
                    </x-lw.input-field>
                    <!-- /Country -->
                    <!-- Phone_Number -->
                    <x-lw.input-field type="number" id="lwPhoneNumberField" data-form-group-class=""
                        :label="__tr('Mobile Number')" name="phone_number" minlength="9"
                        :helpText="__tr('Number should be with country code without 0 or +')" required="true" />
                    <!-- /Phone_Number -->
                    <!-- Language Code -->
                    <x-lw.input-field type="text" id="lwLanguageCodeField" data-form-group-class=""
                        :label="__tr('Language Code')" name="language_code" />
                    <!-- /Language Code -->
                    <!-- Email -->
                    <x-lw.input-field type="email" id="lwEmailField" data-form-group-class="" :label="__tr('Email')"
                        name="email" />
                    <!-- /Email -->
                    <x-lw.input-field type="selectize" data-lw-plugin="lwSelectize" id="lwSelectGroupsField"
                        data-form-group-class="" data-selected=" " :label="__tr('Groups')" name="contact_groups[]"
                        multiple>
                        <x-slot name="selectOptions">
                            <option value="">{{ __tr('Select Groups') }}</option>
                            @foreach($vendorContactGroups as $vendorContactGroup)
                            <option value="{{ $vendorContactGroup['_id'] }}">{{ $vendorContactGroup['title'] }} {{ $vendorContactGroup['status'] == 5  ? __tr('(Archived)') : '' }}</option>
                            @endforeach
                        </x-slot>
                    </x-lw.input-field>
                    <div class="my-3">
                        <x-lw.checkbox id="lwPromotionalOpt" name="whatsapp_opt_out" data-color="#ff0000" data-size="small" value="1" data-lw-plugin="lwSwitchery" :label="__tr('Opt out Marketing Messages')" />
                    </div>
                    <div class="my-3">
                        @if(isAiBotAvailable())
                        <x-lw.checkbox id="lwAiBotEnable" :checked="getVendorSettings('default_enable_flowise_ai_bot_for_users')" name="enable_ai_bot" value="1" data-size="small" 
                            data-lw-plugin="lwSwitchery" :label="__tr('Enable AI Bot')" />
                        @endif
                    </div>
                    <fieldset>
                        <legend>{{ __tr('Other Information') }}</legend>
                        @foreach ($vendorContactCustomFields as $vendorContactCustomField)
                        <x-lw.input-field type="{{ $vendorContactCustomField->input_type }}"
                            id="lwCustomField{{ $vendorContactCustomField->_id }}" data-form-group-class=""
                            :label="$vendorContactCustomField->input_name"
                            name="custom_input_fields[{{ $vendorContactCustomField->_uid }}]" />
                        @endforeach
                    </fieldset>
                </div>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
            </x-lw.form>
            <!--/  Add New Contact Form -->
        </x-lw.modal>
        <!--/ Add New Contact Modal -->

        <!-- Details Contact Modal -->
        <x-lw.modal id="lwDetailsContact" :header="__tr('Contact Details')">
            <!--  Details Contact Form -->
            <!-- Details body -->
            <div id="lwDetailsContactBody" class="lw-form-modal-body"></div>
            <script type="text/template" id="lwDetailsContactBody-template">
                <!-- form fields -->
                <div>
                    <label class="small">{{ __tr('First Name') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.first_name %>
                    </div>
                </div>

                <div>
                    <label class="small">{{ __tr('Last Name') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.last_name %>
                    </div>
                </div>

                <div>
                    <label class="small">{{ __tr('Country') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.country?.name %>
                    </div>
                </div>

                <div>
                    <label class="small">{{ __tr('Mobile Number') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.wa_id %>
                    </div>
                </div>
                <div>
                    <label class="small">{{ __tr('Language Code') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.language_code %>
                    </div>
                </div>

                <div>
                    <label class="small">{{ __tr('Email') }}:</label>
                    <div class="lw-details-item">
                        <%- __tData.email %>
                    </div>
                </div>

                <% if(__tData.assigned_user) { %>
                <div>
                    <label class="small">{{ __tr('Assigned Team Member') }}:</label>
                    <div class="lw-details-item">
                        <i class="fas fa-user-circle text-primary mr-1"></i> <%- __tData.assigned_user.first_name %> <%- __tData.assigned_user.last_name %>
                    </div>
                </div>
                <% } %>
                <% if(!_.isEmpty(__tData.labels)) { %>
                <fieldset class="mt-2">
                    <legend>{{ __tr('Tags') }}</legend>
                    <% _.forEach(__tData.labels, function(label) { %>
                        <span class="badge mr-1" style="background-color: <%- label.bg_color || '#17a2b8' %>; color: <%- label.text_color || '#fff' %>;">
                            <%- label.title %>
                        </span>
                    <% }); %>
                </fieldset>
                <% } %>
                <% if(__tData.contact_notes) { %>
                <div class="mt-2">
                    <label class="small">{{ __tr('Notes') }}:</label>
                    <div class="lw-details-item p-2 bg-light rounded border">
                        <i class="far fa-sticky-note text-warning mr-1"></i> <%- __tData.contact_notes %>
                    </div>
                </div>
                <% } %>
                <fieldset class="mt-2">
                    <legend>{{ __tr('Groups') }}</legend>
                    <% _.forEach(__tData.groups, function(value, key) { %>
                        <span class="badge badge-light">
                            <%- value.title %>
                        </span>
                        <% } ); %>
                </fieldset>
                <fieldset>
                    <legend>{{ __tr('Other Information') }}</legend>
                    @foreach ($vendorContactCustomFields as $vendorContactCustomField)
                    <div class="mb-2">
                        <label class="small">{{ $vendorContactCustomField->input_name }}:</label>
                        <div class="lw-details-item">
                            <%- _.get(_.find(__tData.custom_field_values, {'contact_custom_fields__id' : {{
                                $vendorContactCustomField->_id }} }), 'field_value') %>
                        </div>
                    </div>
                    @endforeach
                </fieldset>
            </script>
            <!--/  Details Contact Form -->
        </x-lw.modal>
        <!--/ Edit Contact Modal -->
         <!--Group description -->
        <div class="ml-3">
            <p class="card-text">{{$groupDescription
            }}</p>
        </div>
         <!--/ Group description -->
        <!-- Edit Contact Modal -->
        @include('contact.contact-edit-modal-partial')
        <!--/ Edit Contact Modal -->

        <div class="col-xl-12" x-cloak x-data="{isSelectedAll:false,selectedContacts: [],selectedGroupsForSelectedContacts:[],
            toggle(id) {
                if (this.selectedContacts.includes(id)) {
                    const index = this.selectedContacts.indexOf(id);
                    this.selectedContacts.splice(index, 1);
                    this.isSelectedAll = false;
                } else {
                    this.selectedContacts.push(id);
                    if($('.dataTables_wrapper table>tbody input[type=checkbox].lw-checkboxes').length == this.selectedContacts.length) {
                        this.isSelectedAll = true;
                    }
                };
            },toggleAll() {
                if(!this.isSelectedAll) {
                    $('.dataTables_wrapper table>tbody input[type=checkbox].lw-checkboxes').not(':checked').trigger('click');
                    this.isSelectedAll = true;
                } else {
                    $('.dataTables_wrapper table>tbody input[type=checkbox].lw-checkboxes:checked').trigger('click');
                    this.isSelectedAll = false;
                }
            },deleteSelectedContacts() {
                var that = this;
                showConfirmation('{{ __tr('Are you sure you want to delete all selected contacts?') }}', function() {
                    __DataRequest.post('{{ route('vendor.contacts.selected.write.delete') }}', {
                        'selected_contacts' : that.selectedContacts
                    });
                }, {
                    confirmButtonText: '{{ __tr('Yes') }}',
                    cancelButtonText: '{{ __tr('No') }}',
                    type: 'error'
                });
            }, assignGroupsToSelectedContacts(){
                var that = this;
                __DataRequest.post('{{ route('vendor.contacts.selected.write.assign_groups') }}', {
                    'selected_contacts' : that.selectedContacts,
                    'selected_groups' : that.selectedGroupsForSelectedContacts
                });
                $('#lwAssignGroups').modal('hide');
                $('.dataTables_wrapper table>tbody input[type=checkbox].lw-checkboxes:checked').trigger('click');
                this.isSelectedAll = false;
            }}" x-init="$('#lwContactList').on( 'draw.dt', function () {
                $('.dataTables_wrapper table>tbody input[type=checkbox].lw-checkboxes:checked').trigger('click');
                isSelectedAll = false;
            } );">

            <!-- Unified Contacts Control Panel -->
            <div class="card shadow-sm border-0 mb-4" style="position: relative; z-index: 5;">
                <div class="card-body p-3" style="overflow: visible;">
                    <!-- Top Row: Tabs on Left, Action Buttons on Right -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 border-bottom mb-3">
                        <div class="mb-2 mb-md-0">
                            <ul class="nav nav-pills" id="lwContactTabs">
                                <li class="nav-item">
                                    <a class="nav-link active font-weight-bold py-2 px-3 lw-contact-tab" data-tab="live_chat" href="javascript:void(0)">
                                        <i class="fab fa-whatsapp text-success mr-1"></i> {{ __tr('Live Chat Contacts') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link font-weight-bold py-2 px-3 lw-contact-tab" data-tab="imported" href="javascript:void(0)">
                                        <i class="fas fa-file-excel text-primary mr-1"></i> {{ __tr('Imported Contacts') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link font-weight-bold py-2 px-3 lw-contact-tab" data-tab="all" href="javascript:void(0)">
                                        <i class="fas fa-users text-muted mr-1"></i> {{ __tr('All Contacts') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="d-flex align-items-center flex-wrap">
                            @if($groupUid)
                            <a class="btn btn-outline-secondary btn-sm mr-2 shadow-sm font-weight-bold" href="{{ route('vendor.contact.group.read.list_view') }}">
                                <i class="fas fa-arrow-left mr-1"></i> {{ __tr('Back to Groups') }}
                            </a>
                            @endif
                            <button type="button" class="btn btn-info btn-sm mr-2 shadow-sm font-weight-bold" data-toggle="modal" data-target="#lwExportDialog">
                                <i class="fas fa-file-export mr-1"></i> {{ __tr('Export Contacts') }}
                            </button>
                            <button type="button" class="btn btn-success btn-sm mr-2 shadow-sm font-weight-bold" data-toggle="modal" data-target="#lwImportContactDialog">
                                <i class="fas fa-file-excel mr-1"></i> {{ __tr('Import Contacts') }}
                            </button>
                            <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold" data-toggle="modal" data-target="#lwAddNewContact">
                                <i class="fas fa-user-plus mr-1"></i> {{ __tr('Create New Contact') }}
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Row: Bulk Actions on Left, Date Filter on Right -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <button x-show="!isSelectedAll" class="btn btn-outline-dark btn-sm mr-2 shadow-sm font-weight-bold" @click="toggleAll">
                                <i class="far fa-check-square mr-1"></i> {{ __tr('Select All') }}
                            </button>
                            <button x-show="isSelectedAll" class="btn btn-outline-dark btn-sm mr-2 shadow-sm font-weight-bold" @click="toggleAll">
                                <i class="far fa-square mr-1"></i> {{ __tr('Unselect All') }}
                            </button>
                            <div class="btn-group" style="position: relative; z-index: 6;">
                                <button :class="!selectedContacts.length ? 'disabled' : ''"
                                    class="btn btn-danger btn-sm dropdown-toggle shadow-sm font-weight-bold" type="button" data-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fas fa-tasks mr-1"></i> {{ __tr('Bulk Actions') }}
                                </button>
                                <div class="dropdown-menu shadow-lg" style="z-index: 10;">
                                    <a class="dropdown-item" @click.prevent="deleteSelectedContacts" href="#"><i class="fas fa-trash text-danger mr-2"></i> {{ __tr('Delete Selected Contacts') }}</a>
                                    <a class="dropdown-item" data-toggle="modal" data-target="#lwAssignGroups" href="#"><i class="fas fa-tags text-primary mr-2"></i> {{ __tr('Assign Group to Selected Contacts') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center">
                            <!-- Quick Date Tags -->
                            <div class="btn-group btn-group-sm mr-2 mb-1 shadow-sm" id="lwQuickDateFilters" role="group" aria-label="Quick Date Filters">
                                <button type="button" class="btn btn-outline-primary font-weight-bold lw-quick-date-btn" data-range="today">
                                    {{ __tr('Today') }}
                                </button>
                                <button type="button" class="btn btn-outline-primary font-weight-bold lw-quick-date-btn" data-range="yesterday">
                                    {{ __tr('Yesterday') }}
                                </button>
                                <button type="button" class="btn btn-outline-primary font-weight-bold lw-quick-date-btn" data-range="7days">
                                    {{ __tr('7 Days') }}
                                </button>
                                <button type="button" class="btn btn-outline-primary font-weight-bold lw-quick-date-btn" data-range="30days">
                                    {{ __tr('30 Days') }}
                                </button>
                            </div>

                            <div class="input-group input-group-sm mr-2 mb-1" style="width: auto;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold"><i class="fas fa-calendar-alt mr-1"></i> {{ __tr('From') }}</span>
                                </div>
                                <input type="date" class="form-control" id="lwFilterFromDate" name="from_date" title="{{ __tr('From Date') }}">
                            </div>
                            <div class="input-group input-group-sm mr-2 mb-1" style="width: auto;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold"><i class="fas fa-calendar-alt mr-1"></i> {{ __tr('To') }}</span>
                                </div>
                                <input type="date" class="form-control" id="lwFilterToDate" name="to_date" title="{{ __tr('To Date') }}">
                            </div>
                            <button type="button" id="lwApplyDateFilter" class="btn btn-sm btn-primary mb-1 mr-1 shadow-sm font-weight-bold">
                                <i class="fas fa-filter mr-1"></i> {{ __tr('Filter') }}
                            </button>
                            <button type="button" id="lwResetDateFilter" class="btn btn-sm btn-outline-secondary mb-1 shadow-sm font-weight-bold" title="{{ __tr('Clear Date Filter') }}">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Unified Contacts Control Panel -->
            <!-- Assign Groups to the selected contacts -->
            <x-lw.modal id="lwAssignGroups" :header="__tr('Assign Groups to Selected Contacts')" :hasForm="true"
                data-pre-callback="appFuncs.clearContainer">
                <!-- form body -->
                <div class="lw-form-modal-body p-4">
                    <!-- form fields form fields -->
                    <x-lw.input-field x-model="selectedGroupsForSelectedContacts" type="selectize"
                        data-lw-plugin="lwSelectize" id="lwSelectGroupsField" data-form-group-class="" data-selected=" "
                        :label="__tr('Groups')" name="contact_groups[]" multiple>
                        <x-slot name="selectOptions">
                            <option value="">{{ __tr('Select Groups') }}</option>
                            @foreach($vendorContactGroups as $vendorContactGroup)
                            <option value="{{ $vendorContactGroup['_id'] }}">{{ $vendorContactGroup['title'] }} {{ $vendorContactGroup['status'] == 5  ? __tr('(Archived)') : '' }}</option>
                            @endforeach
                        </x-slot>
                    </x-lw.input-field>
                </div>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="button" @click="assignGroupsToSelectedContacts" class="btn btn-primary">{{
                        __('Submit') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
                <!--/  Add New Contact Form -->
            </x-lw.modal>
            <!--/ Assign Groups to the selected contacts -->
            <x-lw.datatable data-page-length="100" id="lwContactList" :url="route('vendor.contact.read.list', [
                'groupUid' => $groupUid
            ])">
                <th style="width: 1px;padding:0;" data-orderable="false" data-name="none"></th>
                <th data-orderable="false" data-name="none" data-template="#lwSelectMultipleContactsCheckbox">{{ __tr('Select') }}</th>
                <th data-orderable="true" data-name="first_name">{{ __tr('First Name') }}</th>
                <th data-orderable="true" data-name="last_name">{{ __tr('Last Name') }}</th>
                <th data-name="phone_number">{{ __tr('Mobile Number') }}</th>
                <th data-orderable="false" data-name="null" data-template="#contactAssignedUserColumnTemplate">{{ __tr('Assign Team') }}</th>
                <th data-orderable="false" data-name="null" data-template="#contactTagsColumnTemplate">{{ __tr('Tags') }}</th>
                <th data-orderable="false" data-name="null" data-template="#contactNotesColumnTemplate">{{ __tr('Notes') }}</th>
                <th data-name="language_code">{{ __tr('Language Code') }}</th>
                <th data-orderable="true" data-order-by="true" data-order-type="desc" data-name="created_at">{{ __tr('Created on') }}</th>
                <th data-name="country_name">{{ __tr('Country') }}</th>
                <th data-orderable="true" data-name="email">{{ __tr('Email') }}</th>
                <th data-orderable="true" data-name="whatsapp_opt_out">{{ __tr('Marketing') }}</th>
                @if (isAiBotAvailable())
                <th data-orderable="true" data-name="disable_ai_bot">{{ __tr('AI Bot') }}</th>
                @endif
                <th data-template="#contactActionColumnTemplate" name="null">{{ __tr('Action') }}</th>
            </x-lw.datatable>
        </div>
        <!-- action template -->
        <script type="text/template" id="lwSelectMultipleContactsCheckbox">
            <input @click="toggle('<%- __tData._uid %>')" type="checkbox" name="selected_contacts[]" class="lw-checkboxes custom-checkbox" value="<%- __tData._uid %>">
        </script>
        <!-- Assign Team Column Template -->
        <script type="text/template" id="contactAssignedUserColumnTemplate">
            <% if(__tData.assigned_user_name) { %>
                <span class="badge badge-light border text-dark font-weight-bold" title="{{ __tr('Assigned Team Member') }}">
                    <i class="fas fa-user-circle text-primary mr-1"></i><%- __tData.assigned_user_name %>
                </span>
            <% } else { %>
                <span class="text-muted small">-</span>
            <% } %>
        </script>
        <!-- Tags Column Template -->
        <script type="text/template" id="contactTagsColumnTemplate">
            <% if(!_.isEmpty(__tData.labels)) { %>
                <div class="d-flex flex-wrap" style="gap: 3px;">
                <% _.forEach(__tData.labels, function(label) { %>
                    <span class="badge shadow-sm font-weight-bold" style="background-color: <%- label.bg_color || '#17a2b8' %> !important; color: <%- label.text_color || '#ffffff' %> !important; font-size: 11px; padding: 3px 6px;">
                        <%- label.title %>
                    </span>
                <% }); %>
                </div>
            <% } else { %>
                <span class="text-muted small">-</span>
            <% } %>
        </script>
        <!-- Notes Column Template -->
        <script type="text/template" id="contactNotesColumnTemplate">
            <% if(__tData.contact_notes) { %>
                <div class="small text-dark font-italic" style="max-width: 200px; white-space: normal; line-height: 1.3;" title="<%- __tData.contact_notes %>">
                    <i class="far fa-sticky-note text-warning mr-1"></i><%- __tData.contact_notes %>
                </div>
            <% } else { %>
                <span class="text-muted small">-</span>
            <% } %>
        </script>
        <script type="text/template" id="contactActionColumnTemplate">
            <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Details') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwDetailsContactBody" href="<%= __Utils.apiURL("{{ route('vendor.contact.read.update.data', [ 'contactIdOrUid']) }}", {'contactIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwDetailsContact"><i class="fa fa-info-circle"></i> {{  __tr('Details') }}</a>
            <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Edit') }}" class="lw-btn btn btn-sm btn-default lw-ajax-link-action" data-response-template="#lwEditContactBody" href="<%= __Utils.apiURL("{{ route('vendor.contact.read.update.data', [ 'contactIdOrUid']) }}", {'contactIdOrUid': __tData._uid}) %>"  data-toggle="modal" data-target="#lwEditContact"><i class="fa fa-edit"></i> {{  __tr('Edit') }}</a>
<!--  Delete Action -->
@if(hasVendorAccess('messaging'))
<a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Send Template Message') }}" class="lw-btn btn btn-sm btn-primary" href="<%= __Utils.apiURL("{{ route('vendor.template_message.contact.view', ['contactUid']) }}",{'contactUid': __tData._uid}) %>"><i class="fab fa-whatsapp"></i> {{  __tr('Send Template Message') }}</a> <a data-pre-callback="appFuncs.clearContainer" title="{{  __tr('Chat') }}" class="lw-btn btn btn-sm btn-primary" href="<%= __Utils.apiURL("{{ route('vendor.chat_message.contact.view', ['contactUid']) }}",{'contactUid': __tData._uid}) %>"><i class="fab fa-whatsapp"></i> {{  __tr('Chat') }}</a>
@endif
 <a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.contact.write.delete', [ 'contactIdOrUid']) }}", {'contactIdOrUid': __tData._uid}) %>" class="btn btn-danger btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwDeleteContact-template" title="{{ __tr('Delete') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwContactList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-trash"></i> {{  __tr('Delete') }}</a>
 <!--  Remove Contact Action -->
 @if($currentGroup!=null)
  <a data-method="post" href="<%= __Utils.apiURL("{{ route('vendor.contact.write.remove',['contactIdOrUid', 'groupUid' => $groupUid]) }}",{ 'contactIdOrUid': __tData._uid }) %>" class="btn btn-warning btn-sm lw-ajax-link-action-via-confirm" data-confirm="#lwRemoveContact-template" title="{{ __tr('Remove contact from group') }}" data-callback-params="{{ json_encode(['datatableId' => '#lwContactList']) }}" data-callback="appFuncs.modelSuccessCallback"><i class="fa fa-user-times"></i> {{  __tr('Remove') }}</a> 
 @endif
 
 <!--  Remove Contact Action  -->
    </script>
        <!-- /action template -->
        <!-- Contact delete template -->
        <script type="text/template" id="lwDeleteContact-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to delete this Contact permanently?') }}</p>
    </script>
        <!-- /Contact delete template -->
         <!-- Contact remove template -->
         <script type="text/template" id="lwRemoveContact-template">
            <h2>{{ __tr('Are You Sure!') }}</h2>
            <p>{{ __tr('You want to remove this Contact from this group?') }}</p>
    </script>
        <!-- /Contact remove template -->
    </div>
</div>
@push('appScripts')
<script>
(function($) {
    'use strict';

    // Hook into DataTable preXhr event to inject filters
    $('#lwContactList').on('preXhr.dt', function(e, settings, data) {
        data.from_date = $('#lwFilterFromDate').val();
        data.to_date = $('#lwFilterToDate').val();
        data.contact_tab = $('#lwContactTabs .lw-contact-tab.active').data('tab') || 'live_chat';
        if (!data.order || !data.order.length || data.order[0].column === 0 || data.order[0].column === 1) {
            data.order = [{ column: 9, dir: 'desc' }];
        }
    });

    // Tab change
    $('#lwContactTabs .lw-contact-tab').on('click', function(e) {
        e.preventDefault();
        $('#lwContactTabs .lw-contact-tab').removeClass('active');
        $(this).addClass('active');
        reloadDT('#lwContactList');
    });

    function formatLocalDateYMD(date) {
        var year = date.getFullYear();
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    // Quick date tag filter buttons
    $('.lw-quick-date-btn').on('click', function(e) {
        e.preventDefault();
        var range = $(this).data('range');
        var today = new Date();
        var fromStr = '';
        var toStr = '';

        if (range === 'today') {
            fromStr = formatLocalDateYMD(today);
            toStr = formatLocalDateYMD(today);
        } else if (range === 'yesterday') {
            var yesterday = new Date();
            yesterday.setDate(today.getDate() - 1);
            fromStr = formatLocalDateYMD(yesterday);
            toStr = formatLocalDateYMD(yesterday);
        } else if (range === '7days') {
            var past7 = new Date();
            past7.setDate(today.getDate() - 6);
            fromStr = formatLocalDateYMD(past7);
            toStr = formatLocalDateYMD(today);
        } else if (range === '30days') {
            var past30 = new Date();
            past30.setDate(today.getDate() - 29);
            fromStr = formatLocalDateYMD(past30);
            toStr = formatLocalDateYMD(today);
        }

        $('.lw-quick-date-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('active btn-primary');

        $('#lwFilterFromDate').val(fromStr);
        $('#lwFilterToDate').val(toStr);
        reloadDT('#lwContactList');
    });

    // Date filter click
    $('#lwApplyDateFilter').on('click', function(e) {
        e.preventDefault();
        reloadDT('#lwContactList');
    });

    // Date input change
    $('#lwFilterFromDate, #lwFilterToDate').on('input change', function() {
        $('.lw-quick-date-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
        reloadDT('#lwContactList');
    });

    // Reset date filter
    $('#lwResetDateFilter').on('click', function(e) {
        e.preventDefault();
        $('.lw-quick-date-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
        $('#lwFilterFromDate').val('');
        $('#lwFilterToDate').val('');
        reloadDT('#lwContactList');
    });

    // When Export Modal is opened, automatically sync with active tab and date filter
    $('#lwExportDialog').on('show.bs.modal', function() {
        var currentTab = $('#lwContactTabs .lw-contact-tab.active').data('tab') || 'live_chat';
        var fromDate = $('#lwFilterFromDate').val() || '';
        var toDate = $('#lwFilterToDate').val() || '';

        // Pre-select the corresponding radio button
        $('.lw-export-tab-radio').prop('checked', false);
        if (currentTab === 'live_chat') {
            $('#lwExportTabLiveChat').prop('checked', true);
        } else if (currentTab === 'imported') {
            $('#lwExportTabImported').prop('checked', true);
        } else {
            $('#lwExportTabAll').prop('checked', true);
        }

        // Set hidden date inputs
        $('#lwExportFromDate').val(fromDate);
        $('#lwExportToDate').val(toDate);

        // Update notice text
        if (fromDate && toDate) {
            $('#lwExportDateText').html('{{ __tr('Date Range:') }} <strong>' + fromDate + '</strong> {{ __tr('to') }} <strong>' + toDate + '</strong>');
        } else if (fromDate) {
            $('#lwExportDateText').html('{{ __tr('Date Range: From') }} <strong>' + fromDate + '</strong>');
        } else if (toDate) {
            $('#lwExportDateText').html('{{ __tr('Date Range: Up to') }} <strong>' + toDate + '</strong>');
        } else {
            $('#lwExportDateText').text('{{ __tr('Date Range: All Dates') }}');
        }
    });

    // Close modal after triggering download form submit
    $('#lwExportContactsForm').on('submit', function() {
        setTimeout(function() {
            $('#lwExportDialog').modal('hide');
        }, 600);
    });

    // FilePond upload detector
    setInterval(function() {
        var docName = $('#lwImportDocument').val();
        if (docName && docName.length > 0) {
            $('#lwBtnGoToPreview').prop('disabled', false);
        } else {
            $('#lwBtnGoToPreview').prop('disabled', true);
        }
    }, 400);

    // Import Preview Logic
    var previewRowsData = [];

    $('#lwBtnGoToPreview').on('click', function() {
        var docName = $('#lwImportDocument').val();
        if (!docName) {
            showWarnMessage('{{ __tr('Please select and upload an excel file first.') }}');
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{ __tr('Reading Excel...') }}');

        __DataRequest.post('{{ route('vendor.contact.write.import_preview') }}', {
            document_name: docName
        }, function(response) {
            $btn.prop('disabled', false).html('<i class="fas fa-table mr-1"></i> {{ __tr('Preview & Select Contacts') }}');

            if (response.reaction == 1) {
                previewRowsData = response.data.rows || [];
                renderPreviewTable(previewRowsData);
                $('#lwImportStep1').hide();
                $('#lwImportStep2').show();
            } else {
                showErrorMessage(response.message || '{{ __tr('Failed to read Excel file.') }}');
            }
        });
    });

    function renderPreviewTable(rows) {
        var $tbody = $('#lwImportPreviewTableBody').empty();
        var selectedCount = 0;
        var validCount = 0;
        var invalidCount = 0;

        $('#lwPreviewBadgeTotal').text(rows.length + ' {{ __tr('Found') }}');

        if (!rows.length) {
            $tbody.append('<tr><td colspan="7" class="text-muted p-4">{{ __tr('No rows found in this file.') }}</td></tr>');
            updateSelectionCounts(0, 0, 0);
            return;
        }

        $.each(rows, function(idx, row) {
            var isInvalid = !row.is_valid;
            if (isInvalid) {
                invalidCount++;
            } else {
                validCount++;
                selectedCount++;
            }

            var tr = $('<tr class="lw-preview-row"></tr>');
            if (isInvalid) {
                tr.addClass('table-warning text-muted');
            }

            var cbHtml = '<input type="checkbox" class="lw-preview-row-cb custom-checkbox" data-row-idx="' + row.row_index + '" ' + (isInvalid ? 'disabled' : 'checked') + '>';
            tr.append('<td>' + cbHtml + '</td>');
            tr.append('<td class="small font-weight-bold text-muted">' + (idx + 1) + '</td>');
            tr.append('<td class="text-left font-weight-bold">' + $('<div>').text(row.first_name || '-').html() + '</td>');
            tr.append('<td class="text-left">' + $('<div>').text(row.last_name || '-').html() + '</td>');
            tr.append('<td><code>' + $('<div>').text(row.phone_number || '-').html() + '</code></td>');
            tr.append('<td class="text-left small">' + $('<div>').text(row.email || '-').html() + '</td>');

            var statusBadge = row.is_valid 
                ? '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> ' + row.status_message + '</span>'
                : '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle mr-1"></i> ' + row.status_message + '</span>';
            tr.append('<td>' + statusBadge + '</td>');

            $tbody.append(tr);
        });

        updateSelectionCounts(rows.length, selectedCount, invalidCount);
    }

    function updateSelectionCounts(total, selected, invalid) {
        $('#lwPreviewBadgeTotal').text(total + ' {{ __tr('Total') }}');
        $('#lwPreviewBadgeSelected').text(selected + ' {{ __tr('Selected') }}');
        if (invalid > 0) {
            $('#lwPreviewBadgeInvalid').text(invalid + ' {{ __tr('Invalid') }}').show();
        } else {
            $('#lwPreviewBadgeInvalid').hide();
        }

        $('#lwBtnConfirmImportText').text('{{ __tr('Import Selected Contacts') }} (' + selected + ')');
        $('#lwBtnConfirmImport').prop('disabled', selected === 0);
    }

    // Toggle row checkboxes
    $(document).on('change', '.lw-preview-row-cb', function() {
        var total = previewRowsData.length;
        var selected = $('.lw-preview-row-cb:checked').length;
        var invalid = $('.lw-preview-row-cb:disabled').length;
        updateSelectionCounts(total, selected, invalid);
    });

    // Select all valid checkbox
    $('#lwPreviewSelectAll').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.lw-preview-row-cb:not(:disabled)').prop('checked', isChecked);
        var total = previewRowsData.length;
        var selected = $('.lw-preview-row-cb:checked').length;
        var invalid = $('.lw-preview-row-cb:disabled').length;
        updateSelectionCounts(total, selected, invalid);
    });

    // Quick search in preview table
    $('#lwPreviewSearchInput').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('#lwImportPreviewTableBody tr.lw-preview-row').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });

    // Back to upload view
    $('#lwBtnBackToUpload, #lwBtnCancelPreviewStep').on('click', function() {
        $('#lwImportStep2').hide();
        $('#lwImportStep1').show();
    });

    // Confirm final import
    $('#lwBtnConfirmImport').on('click', function() {
        var selectedIndices = [];
        $('.lw-preview-row-cb:checked').each(function() {
            selectedIndices.push($(this).data('row-idx'));
        });

        if (!selectedIndices.length) {
            showWarnMessage('{{ __tr('Please select at least one contact to import.') }}');
            return;
        }

        var docName = $('#lwImportDocument').val();
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{ __tr('Importing...') }}');

        __DataRequest.post('{{ route('vendor.contact.write.import') }}', {
            document_name: docName,
            selected_row_indices: selectedIndices
        }, function(response) {
            $btn.prop('disabled', false).html('<i class="fas fa-check mr-1"></i> <span id="lwBtnConfirmImportText">{{ __tr('Import Selected Contacts') }}</span>');

            if (response.reaction == 1) {
                $('#lwImportContactDialog').modal('hide');
                $('#lwImportStep2').hide();
                $('#lwImportStep1').show();
                previewRowsData = [];

                // Switch to Imported Contacts tab to show the imported contacts
                $('#lwContactTabs .lw-contact-tab').removeClass('active');
                $('#lwContactTabs .lw-contact-tab[data-tab="imported"]').addClass('active');

                reloadDT('#lwContactList');
                showSuccessMessage(response.message);
            } else {
                showErrorMessage(response.message || '{{ __tr('Failed to import contacts.') }}');
            }
        });
    });

    window.onUpdateContactDetails = function(responseData, callbackParams) {
        appFuncs.modelSuccessCallback(responseData, callbackParams);
    }
})(jQuery);
</script>
@endpush
@endsection()