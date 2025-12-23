@extends('layouts.app')

@section('title', 'CLO–PLO Analysis – ' . $courseName)

@section('content')
<div class="py-6 overflow-x-hidden" x-data="cloPloController()" @open-edit-modal.window="openEditModalById($event.detail.id)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">CLO–PLO Analysis – {{ $courseName }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Configure CLO mapping and assessment eligibility</p>
            </div>
            @if(auth()->user()->isAdmin())
            <button @click="openAddCloModal()"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New CLO
            </button>
            @endif
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <!-- Info Banner -->
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-semibold mb-1">CLO Assessment Eligibility Rules:</p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        <li>CLO must be <strong>Active</strong> to appear in the system</li>
                        <li>CLO must map to <strong>at least one PLO</strong> to be assessment-eligible</li>
                        <li>CLO must have <strong>"Allow for Assessment"</strong> enabled to appear in assessment creation</li>
                        <li>Only approved CLOs can be selected when creating assessments</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- CLO Settings Card (Admin Only) -->
        @if(auth()->user()->isAdmin())
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Current CLO Count:</span>
                    <span class="px-3 py-1 bg-[#0084C5] text-white rounded-full text-sm font-bold">{{ $cloCount ?? count($cloMappings) }}</span>
                </div>
                <form action="{{ route('academic.' . strtolower($courseCode) . '.clo-plo.update-count') }}" method="POST" class="flex items-center gap-3">
                    @csrf
                    <label class="text-sm text-gray-600 dark:text-gray-400">Set CLO Count:</label>
                    <input type="number" 
                           name="clo_count" 
                           value="{{ $cloCount ?? count($cloMappings) }}" 
                           min="1" 
                           max="20"
                           class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-center">
                    <button type="submit" 
                            class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                        Update
                    </button>
                </form>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                Changing the CLO count will add new CLO slots. Existing CLO mappings will not be affected.
            </p>
        </div>
        @endif


        <!-- CLO Mapping Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">CLO Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">CLO Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Mapped PLO(s)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Assessment Allowed</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($cloMappings as $mapping)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-[#003A6C] dark:text-[#0084C5]">{{ $mapping->clo_code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-200">
                                    {{ $mapping->clo_description ?: 'No description' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @forelse($mapping->ploRelationships as $relationship)
                                        <div class="flex items-start gap-2">
                                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded flex-shrink-0">
                                                {{ $relationship->plo_code }}
                                            </span>
                                            @if($relationship->plo_description)
                                                <span class="text-xs text-gray-600 dark:text-gray-400 italic">
                                                    {{ Str::limit($relationship->plo_description, 50) }}
                                                </span>
                                            @endif
                                        </div>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">No PLO mapping</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($mapping->allow_for_assessment && $mapping->isEligibleForAssessment())
                                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full">✅ Yes</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">❌ No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($mapping->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(auth()->user()->isAdmin())
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button"
                                                x-data
                                                @click="$dispatch('open-edit-modal', { id: {{ $mapping->id }} })"
                                                class="px-3 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                                            Edit
                                        </button>
                                        <form action="{{ route('academic.' . strtolower($courseCode) . '.clo-plo.destroy', $mapping) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete {{ $mapping->clo_code }}? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">View only</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No CLO mappings configured</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        @if(auth()->user()->isAdmin())
                                            Click "Add New CLO" to get started.
                                        @else
                                            Please contact an administrator to configure CLO mappings.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Add/Edit Modal -->
    @if(auth()->user()->isAdmin())
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;"
         @keydown.escape.window="closeModal()">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full"
                 @click.stop>
                <form :action="formAction" method="POST" id="clo-plo-form" @submit.prevent="submitForm()">
                    @csrf
                    <template x-if="isEditMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">
                            <span x-text="isEditMode ? 'Edit CLO Mapping: ' + currentCloCode : 'Add New CLO'"></span>
                        </h3>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
                        <!-- CLO Number (for new CLOs) -->
                        <div x-show="!isEditMode">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                CLO Number <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600 dark:text-gray-400">CLO</span>
                                <input type="number" 
                                       name="clo_number"
                                       x-model="newCloNumber"
                                       min="1"
                                       max="99"
                                       class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-center"
                                       placeholder="1">
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter the CLO number (e.g., 1 for CLO1, 2 for CLO2)</p>
                        </div>
                        
                        <!-- Hidden CLO code -->
                        <input type="hidden" name="clo_code" :value="isEditMode ? currentCloCode : ('CLO' + newCloNumber)">
                        
                        <!-- CLO Description -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                CLO Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="clo_description" 
                                      x-model="currentDescription"
                                      rows="3"
                                      required
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white"
                                      placeholder="Enter CLO description..."></textarea>
                        </div>

                        <!-- PLO Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Select PLO(s) <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Hold Ctrl/Cmd to select multiple PLOs. You can add descriptions for each PLO below.</p>
                            <select id="plo-select"
                                    multiple
                                    size="6"
                                    x-model="selectedPloCodes"
                                    @change="updatePloDescriptions()"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                @foreach($ploCodes as $plo)
                                    <option value="{{ $plo }}">{{ $plo }}</option>
                                @endforeach
                            </select>
                            <!-- Hidden inputs for selected PLO codes -->
                            <template x-for="(ploCode, index) in selectedPloCodes" :key="'plo-input-' + ploCode">
                                <input type="hidden" :name="'plo_codes[' + index + ']'" :value="ploCode">
                            </template>
                        </div>

                        <!-- PLO Descriptions (Dynamic) -->
                        <div x-show="selectedPloCodes.length > 0" class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                PLO Descriptions
                            </label>
                            <template x-for="(ploCode, index) in selectedPloCodes" :key="'plo-desc-' + ploCode">
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-gray-700/50">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1" x-text="ploCode"></label>
                                    <textarea :name="'plo_descriptions[' + index + ']'"
                                              x-model="ploDescriptions[ploCode]"
                                              rows="2"
                                              class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white"
                                              :placeholder="'Enter description for ' + ploCode + '...'"></textarea>
                                </div>
                            </template>
                        </div>

                        <!-- Toggles -->
                        <div class="space-y-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       x-model="currentIsActive"
                                       class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="allow_for_assessment" 
                                       value="1"
                                       x-model="currentAllowForAssessment"
                                       class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Allow for Assessment</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end space-x-3">
                        <button type="button" 
                                @click="closeModal()"
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                            <span x-text="isEditMode ? 'Save Changes' : 'Add CLO'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

@php
$cloDataForJs = $cloMappings->map(function($mapping) {
    return [
        'id' => $mapping->id,
        'clo_code' => $mapping->clo_code,
        'clo_description' => $mapping->clo_description ?? '',
        'is_active' => $mapping->is_active,
        'allow_for_assessment' => $mapping->allow_for_assessment,
        'plo_relationships' => $mapping->ploRelationships->map(function($r) {
            return ['code' => $r->plo_code, 'description' => $r->plo_description ?? ''];
        })->toArray()
    ];
})->keyBy('id')->toArray();
@endphp

<script>
// Store CLO data globally for easy access
var cloPloData = {!! json_encode($cloDataForJs) !!};

function cloPloController() {
    return {
        showModal: false,
        isEditMode: false,
        currentMappingId: null,
        currentCloCode: '',
        currentDescription: '',
        currentIsActive: true,
        currentAllowForAssessment: false,
        currentPloRelationships: [],
        selectedPloCodes: [],
        ploDescriptions: {},
        formAction: '',
        newCloNumber: {{ ($cloCount ?? count($cloMappings)) + 1 }},

        // Called from custom event dispatch
        openEditModalById(mappingId) {
            const mapping = cloPloData[mappingId];
            if (!mapping) {
                console.error('Mapping not found for ID:', mappingId);
                alert('Error: Could not find CLO data. Please refresh the page.');
                return;
            }
            this.openEditModal(
                mapping.id,
                mapping.clo_code,
                mapping.clo_description,
                mapping.is_active,
                mapping.allow_for_assessment,
                mapping.plo_relationships
            );
        },

        openAddCloModal() {
            this.isEditMode = false;
            this.currentMappingId = null;
            this.currentCloCode = '';
            this.currentDescription = '';
            this.currentIsActive = true;
            this.currentAllowForAssessment = false;
            this.currentPloRelationships = [];
            this.selectedPloCodes = [];
            this.ploDescriptions = {};
            this.newCloNumber = {{ ($cloCount ?? count($cloMappings)) + 1 }};
            
            const courseCode = '{{ strtolower($courseCode) }}';
            this.formAction = '/academic/' + courseCode + '/clo-plo';
            
            this.showModal = true;
        },

        openEditModal(mappingId, cloCode, description, isActive, allowForAssessment, ploRelationships) {
            this.isEditMode = true;
            this.currentMappingId = mappingId;
            this.currentCloCode = cloCode;
            this.currentDescription = description || '';
            this.currentIsActive = isActive;
            this.currentAllowForAssessment = allowForAssessment;
            this.currentPloRelationships = ploRelationships || [];
            
            // Initialize selected PLO codes and descriptions
            this.selectedPloCodes = this.currentPloRelationships.map(r => r.code);
            this.ploDescriptions = {};
            this.currentPloRelationships.forEach(r => {
                this.ploDescriptions[r.code] = r.description || '';
            });
            
            // Set form action based on course
            const courseCode = '{{ strtolower($courseCode) }}';
            if (mappingId) {
                this.formAction = '/academic/' + courseCode + '/clo-plo/' + mappingId;
            } else {
                this.formAction = '/academic/' + courseCode + '/clo-plo';
            }
            
            this.showModal = true;
            
            // Wait for Alpine to update the DOM, then set select values
            this.$nextTick(() => {
                const select = document.getElementById('plo-select');
                if (select) {
                    Array.from(select.options).forEach(option => {
                        option.selected = this.selectedPloCodes.includes(option.value);
                    });
                }
            });
        },

        updatePloDescriptions() {
            // When PLOs are selected/deselected, update descriptions object
            const newDescriptions = {};
            this.selectedPloCodes.forEach(code => {
                newDescriptions[code] = this.ploDescriptions[code] || '';
            });
            this.ploDescriptions = newDescriptions;
        },

        closeModal() {
            this.showModal = false;
            this.isEditMode = false;
            this.currentMappingId = null;
            this.currentCloCode = '';
            this.currentDescription = '';
            this.currentIsActive = true;
            this.currentAllowForAssessment = false;
            this.currentPloRelationships = [];
            this.selectedPloCodes = [];
            this.ploDescriptions = {};
            this.newCloNumber = {{ ($cloCount ?? count($cloMappings)) + 1 }};
        },

        submitForm() {
            // Get the form element
            const form = document.getElementById('clo-plo-form');
            if (!form) return;
            
            // Validate that at least one PLO is selected
            if (this.selectedPloCodes.length === 0) {
                alert('Please select at least one PLO.');
                return;
            }
            
            // Validate CLO description
            if (!this.currentDescription.trim()) {
                alert('Please enter a CLO description.');
                return;
            }
            
            // For new CLOs, validate CLO number
            if (!this.isEditMode && (!this.newCloNumber || this.newCloNumber < 1)) {
                alert('Please enter a valid CLO number.');
                return;
            }
            
            // Submit the form normally
            form.submit();
        },

        initializeMissingClos() {
            const missingClos = @json($missingCloCodes);
            if (missingClos.length === 0) return;
            
            // Open modal for first missing CLO
            const firstMissing = missingClos[0];
            const cloNumber = parseInt(firstMissing.replace('CLO', ''));
            this.newCloNumber = cloNumber;
            this.isEditMode = false;
            this.currentMappingId = null;
            this.currentCloCode = '';
            this.currentDescription = '';
            this.currentIsActive = true;
            this.currentAllowForAssessment = false;
            this.selectedPloCodes = [];
            this.ploDescriptions = {};
            
            const courseCode = '{{ strtolower($courseCode) }}';
            this.formAction = '/academic/' + courseCode + '/clo-plo';
            
            this.showModal = true;
        }
    }
}
</script>
@endsection
