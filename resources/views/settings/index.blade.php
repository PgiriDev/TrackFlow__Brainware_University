@extends('layouts.app')

@section('title', 'Settings')
@section('breadcrumb', 'Settings')

@push('styles')
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <!-- Cropper.js Library - Load early so it's available when needed -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
@endpush

@section('content')
    <script>
        // Define profile picture functions early before DOM renders
        window.cropperSettings = null;
        window.cameraStreamSettings = null;
        window.isRecroppingSettings = false;
        window.cropperReady = false;
        window.cropperCanvasData = null;

        // Close dynamic modal function
        window.closeCropModalSettingsDynamic = function () {
            const modal = document.getElementById('cropModalSettingsDynamic');
            if (modal) {
                modal.remove();
            }
            if (window.cropperSettings) {
                window.cropperSettings.destroy();
                window.cropperSettings = null;
            }
        };

        // Zoom handler for dynamic modal
        window.handleZoomSettingsDynamic = function (value) {
            if (window.cropperSettings && window.cropperReady && window.cropperCanvasData) {
                const ratio = parseFloat(value);
                // Slider goes from -0.5 to 1, map to 0.5x to 2x zoom
                const zoomMultiplier = 1 + ratio;
                const newWidth = window.cropperCanvasData.naturalWidth * zoomMultiplier;
                const newHeight = window.cropperCanvasData.naturalHeight * zoomMultiplier;

                // Calculate center position
                const containerData = window.cropperSettings.getContainerData();
                const newLeft = (containerData.width - newWidth) / 2;
                const newTop = (containerData.height - newHeight) / 2;

                window.cropperSettings.setCanvasData({
                    left: newLeft,
                    top: newTop,
                    width: newWidth,
                    height: newHeight
                });
            }
        };

        // Reset cropper for dynamic modal
        window.resetCropperSettingsDynamic = function () {
            if (window.cropperSettings) {
                window.cropperSettings.reset();
                const zoomRange = document.getElementById('zoomRangeSettingsDynamic');
                if (zoomRange) zoomRange.value = '0';
                // Store canvas data again after reset
                setTimeout(function () {
                    if (window.cropperSettings) {
                        window.cropperCanvasData = window.cropperSettings.getCanvasData();
                    }
                }, 100);
            }
        };

        // Save cropped image for dynamic modal
        window.saveCroppedImageSettingsDynamic = async function () {
            if (!window.cropperSettings) {
                console.error('Cropper not initialized');
                return;
            }

            try {
                const canvas = window.cropperSettings.getCroppedCanvas({
                    width: 256,
                    height: 256,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                if (!canvas) {
                    throw new Error('Failed to get cropped canvas');
                }

                // Convert canvas to base64 string
                const base64Data = canvas.toDataURL('image/png');

                try {
                    const response = await fetch('/profile/upload-picture', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            profile_picture: base64Data
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        window.closeCropModalSettingsDynamic();
                        if (typeof popupSuccess === 'function') {
                            popupSuccess('Profile picture updated successfully! Page will reload...', 'Success');
                        }
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw new Error(data.message || 'Upload failed');
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    if (typeof popupError === 'function') {
                        popupError('Failed to upload image: ' + error.message, 'Error');
                    } else {
                        alert('Failed to upload image: ' + error.message);
                    }
                }
            } catch (error) {
                console.error('Save cropped image error:', error);
                if (typeof popupError === 'function') {
                    popupError('Failed to upload image: ' + error.message, 'Error');
                } else {
                    alert('Failed to upload image: ' + error.message);
                }
            }
        };

        // Open crop modal function
        window.openCropModalSettings = function (imageSrc) {
            console.log('openCropModalSettings called');

            // Remove any existing modal first
            const existingModal = document.getElementById('cropModalSettingsDynamic');
            if (existingModal) {
                existingModal.remove();
            }

            // Destroy existing cropper
            if (window.cropperSettings) {
                window.cropperSettings.destroy();
                window.cropperSettings = null;
            }

            // Reset ready flag
            window.cropperReady = false;

            // Create modal using DOM methods (avoids Trusted Types issues)
            const modal = document.createElement('div');
            modal.id = 'cropModalSettingsDynamic';
            modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85); z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 20px;';

            const modalContent = document.createElement('div');
            modalContent.style.cssText = 'background: white; border-radius: 12px; max-width: 900px; width: 100%; max-height: 90vh; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);';

            // Header
            const header = document.createElement('div');
            header.style.cssText = 'padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;';

            const title = document.createElement('h3');
            title.style.cssText = 'font-size: 1.25rem; font-weight: 600; color: #111827; margin: 0;';
            title.textContent = 'Crop Profile Picture';

            const closeBtn = document.createElement('button');
            closeBtn.style.cssText = 'background: none; border: none; cursor: pointer; padding: 8px; color: #6b7280; font-size: 24px;';
            closeBtn.innerHTML = '&times;';
            closeBtn.onclick = window.closeCropModalSettingsDynamic;

            header.appendChild(title);
            header.appendChild(closeBtn);

            // Body
            const body = document.createElement('div');
            body.style.cssText = 'padding: 20px;';

            const imageContainer = document.createElement('div');
            imageContainer.style.cssText = 'height: 400px; max-height: 50vh; background: #f3f4f6; border-radius: 8px;';

            const cropImage = document.createElement('img');
            cropImage.id = 'cropImageSettingsDynami                c';
            cropImage.style.cssText = 'display: block; max-width: 100%; max-height: 100%;';
            cropImage.src = imageSrc;

            imageContainer.appendChild(cropImage);

            // Zoom controls
            const zoomControls = document.createElement('div');
            zoomControls.style.cssText = 'margin-top: 16px; display: flex; align-items: center; gap: 16px;';

            const zoomLabel = document.createElement('label');
            zoomLabel.style.cssText = 'font-size: 14px; font-weight: 500; color: #374151;';
            zoomLabel.textContent = 'Zoom:';

            const zoomRange = document.createElement('input');
            zoomRange.type = 'range';
            zoomRange.id = 'zoomRangeSettingsDynamic';
            zoomRange.min = '-1';
            zoomRange.max = '1';
            zoomRange.step = '0.01';
            zoomRange.value = '0';
            zoomRange.style.cssText = 'flex: 1;';
            zoomRange.oninput = function () { window.handleZoomSettingsDynamic(this.value); };

            const resetBtn = document.createElement('button');
            resetBtn.style.cssText = 'padding: 6px 12px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;';
            resetBtn.textContent = 'Reset';
            resetBtn.onclick = window.resetCropperSettingsDynamic;

            zoomControls.appendChild(zoomLabel);
            zoomControls.appendChild(zoomRange);
            zoomControls.appendChild(resetBtn);

            body.appendChild(imageContainer);
            body.appendChild(zoomControls);

            // Footer
            const footer = document.createElement('div');
            footer.style.cssText = 'padding: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px;';

            const cancelBtn = document.createElement('button');
            cancelBtn.style.cssText = 'padding: 10px 24px; background: #e5e7eb; color: #111827; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;';
            cancelBtn.textContent = 'Cancel';
            cancelBtn.onclick = window.closeCropModalSettingsDynamic;

            const doneBtn = document.createElement('button');
            doneBtn.style.cssText = 'padding: 10px 24px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;';
            doneBtn.textContent = 'Done';
            doneBtn.onclick = window.saveCroppedImageSettingsDynamic;

            footer.appendChild(cancelBtn);
            footer.appendChild(doneBtn);

            // Assemble modal
            modalContent.appendChild(header);
            modalContent.appendChild(body);
            modalContent.appendChild(footer);
            modal.appendChild(modalContent);

            // Add to body
            document.body.appendChild(modal);
            console.log('Modal added to body');

            // Function to initialize cropper
            function initCropper() {
                console.log('Initializing Cropper...');
                if (typeof Cropper === 'undefined') {
                    console.error('Cropper library not loaded!');
                    alert('Cropper library not loaded. Please refresh the page.');
                    return;
                }

                // Destroy any existing cropper first
                if (window.cropperSettings) {
                    window.cropperSettings.destroy();
                    window.cropperSettings = null;
                }

                window.cropperSettings = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: true,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    minContainerWidth: 200,
                    minContainerHeight: 200,
                    checkCrossOrigin: false,
                    ready: function () {
                        console.log('Cropper ready!');
                        window.cropperReady = true;
                        // Store canvas data for zoom control
                        window.cropperCanvasData = window.cropperSettings.getCanvasData();
                        console.log('Canvas data stored:', window.cropperCanvasData);
                    }
                });
            }

            // Wait for image to load then initialize cropper
            if (cropImage.complete && cropImage.naturalHeight !== 0) {
                setTimeout(initCropper, 100);
            } else {
                cropImage.onload = function () {
                    console.log('Image loaded');
                    setTimeout(initCropper, 100);
                };
                cropImage.onerror = function () {
                    console.error('Image failed to load');
                    alert('Failed to load image. Please try again.');
                    window.closeCropModalSettingsDynamic();
                };
            }
        };

        // Handle profile picture selection
        window.handleProfilePictureSettings = function (event) {
            const file = event.target.files[0];
            console.log('File selected:', file);

            if (file && file.type.startsWith('image/')) {
                window.isRecroppingSettings = false;
                const reader = new FileReader();
                reader.onload = function (e) {
                    console.log('Image loaded, opening crop modal...');
                    window.openCropModalSettings(e.target.result);
                };
                reader.onerror = function (error) {
                    console.error('FileReader error:', error);
                    if (typeof popupError === 'function') {
                        popupError('Failed to read image file', 'Error');
                    }
                };
                reader.readAsDataURL(file);
            } else if (file) {
                if (typeof popupError === 'function') {
                    popupError('Please select a valid image file (JPG, PNG, or GIF)', 'Invalid File');
                }
            }
        };

        // Recrop existing profile picture
        window.recropProfilePictureSettings = function () {
            const currentPicture = document.getElementById('settingsProfilePictureImg');
            if (currentPicture && currentPicture.src) {
                window.isRecroppingSettings = true;
                window.openCropModalSettings(currentPicture.src);
            }
        };

        // Remove profile picture
        window.removeProfilePictureSettings = async function () {
            if (!confirm('Are you sure you want to remove your profile picture?')) return;

            try {
                const response = await fetch('/profile/delete-picture', {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    if (typeof popupSuccess === 'function') {
                        popupSuccess('Profile picture removed successfully', 'Success');
                    }
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (typeof popupError === 'function') {
                        popupError(data.message || 'Failed to remove picture', 'Error');
                    }
                }
            } catch (error) {
                console.error('Remove picture error:', error);
                if (typeof popupError === 'function') {
                    popupError('Failed to remove profile picture', 'Error');
                }
            }
        };

        // Legacy close function for static modal
        window.closeCropModalSettings = function () {
            const modal = document.getElementById('cropModalSettings');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.add('hidden');
            }
            if (window.cropperSettings) {
                window.cropperSettings.destroy();
                window.cropperSettings = null;
            }
        };

        // Legacy zoom handler for static modal
        window.handleZoomSettings = function (value) {
            if (window.cropperSettings) {
                const ratio = parseFloat(value);
                window.cropperSettings.zoom(ratio * 0.1);
            }
        };

        // Legacy reset for static modal
        window.resetCropperSettings = function () {
            if (window.cropperSettings) {
                window.cropperSettings.reset();
                const zoomRange = document.getElementById('zoomRangeSettings');
                if (zoomRange) zoomRange.value = 0;
            }
        };

        // Open zoom modal
        window.openZoomModalSettings = function () {
            const img = document.getElementById('settingsProfilePictureImg');
            if (img && img.src) {
                document.getElementById('zoomImageSettings').src = img.src;
                document.getElementById('zoomModalSettings').classList.remove('hidden');
                document.getElementById('zoomModalSettings').classList.add('flex');
            }
        };

        // Close zoom modal
        window.closeZoomModalSettings = function () {
            const modal = document.getElementById('zoomModalSettings');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        };
    </script>
    @php
        $userId = session('user_id');
        if (!$userId) {
            redirect()->route('login')->send();
            exit();
        }

        $user = DB::table('users')->where('id', $userId)->first();
        $userPrefs = DB::table('user_preferences')->where('user_id', $userId)->first();

        if (!$user) {
            redirect()->route('login')->send();
            exit();
        }

        $userName = $user->name ?? session('user_name', '');
        $userEmail = $user->email ?? session('user_email', '');
        $userPhone = $user->phone ?? '';
        $userCurrency = $user->currency ?? 'INR';
        $profilePicture = $user->profile_picture ?? null;
        $compactMode = $user->compact_mode ?? 0;
        $showDecimals = $user->show_decimals ?? 1;
        $userLanguage = $user->language ?? 'en';
        $dateFormat = $userPrefs->date_format ?? 'Y-m-d';
        $emailNotif = $userPrefs->email_notifications ?? 1;
        $budgetAlerts = $userPrefs->budget_alerts ?? 1;
        $transactionAlerts = $userPrefs->transaction_alerts ?? 1;
        $largeTransAlerts = $userPrefs->large_transaction_alerts ?? 1;
        $largeTransThreshold = $userPrefs->large_transaction_threshold ?? 333.87;
        $pushNotif = $userPrefs->push_notifications ?? 1;
        $weeklySummary = $userPrefs->weekly_summary ?? 1;
        $goalProgress = $userPrefs->goal_progress ?? 1;
        $groupExpense = $userPrefs->group_expense ?? 1;
        $loginAlerts = $userPrefs->login_alerts ?? 1;
        $newDeviceAlerts = $userPrefs->new_device_alerts ?? 1;

    @endphp

    <!-- Colorful Glassmorphism Page Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute inset-0 bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        </div>
        <!-- Decorative Orbs -->
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-cyan-300/40 to-blue-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-cyan-600/10 dark:to-blue-700/10">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-pink-300/40 to-rose-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-pink-600/10 dark:to-rose-700/10">
        </div>
        <div
            class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-violet-300/30 to-purple-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-violet-600/10 dark:to-purple-700/10">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-indigo-300/30 to-blue-400/30 rounded-full blur-3xl dark:from-indigo-600/10 dark:to-blue-700/10">
        </div>
        <div
            class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-fuchsia-300/30 to-pink-400/30 rounded-full blur-3xl dark:from-fuchsia-600/10 dark:to-pink-700/10">
        </div>
    </div>

    <div class="animate-fade-in relative" x-data="{ activeTab: 'profile' }">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Settings</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your account and application preferences</p>
        </div>

        <!-- Tabs - Sticky Navigation -->
        <div class="sticky top-0 z-20 bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 card-shadow mb-6 transition-shadow"
            x-data="{ isSticky: false }"
            x-init="window.addEventListener('scroll', () => { isSticky = window.scrollY > 100 })"
            :class="{ 'shadow-lg': isSticky }">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button @click="activeTab = 'profile'"
                        :class="activeTab === 'profile' ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <i class="fas fa-user mr-2"></i>Profile
                    </button>
                    <button @click="activeTab = 'security'"
                        :class="activeTab === 'security' ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <i class="fas fa-shield-alt mr-2"></i>Security
                    </button>
                    <button @click="activeTab = 'notifications'"
                        :class="activeTab === 'notifications' ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <i class="fas fa-bell mr-2"></i>Notifications
                    </button>
                </nav>
            </div>
        </div>

        <!-- Profile Tab Content -->
        <div x-show="activeTab === 'profile'" x-cloak>
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 card-shadow p-6">
                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-user text-2xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Profile Settings</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage your personal information</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Picture Section -->
                <div class="mb-10">
                    <div
                        class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-2xl shadow-lg p-8 max-w-9xl mx-auto">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-8">
                            <div class="flex flex-col items-center">
                                <div id="settingsProfilePictureContainer" class="relative group">
                                    @if($profilePicture)
                                        <img id="settingsProfilePictureImg" src="{{ $profilePicture }}" alt="Profile"
                                            class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-xl cursor-pointer hover:opacity-90 transition-opacity"
                                            onclick="openZoomModalSettings()">
                                    @else
                                        <div
                                            class="w-32 h-32 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center border-4 border-white dark:border-gray-700 shadow-xl">
                                            <span
                                                class="text-4xl font-bold text-white">{{ strtoupper(substr($userName, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                    <div
                                        class="absolute inset-0 rounded-full bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex items-center justify-center pointer-events-none">
                                        <i
                                            class="fas fa-camera text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-4 text-center">JPG, PNG or GIF<br>Max
                                    5MB</p>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Profile Picture</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Upload a new profile picture or
                                    update your existing one</p>
                                <div class="flex flex-wrap gap-3">
                                    <label for="profilePictureUploadSettings"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all cursor-pointer transform hover:scale-105">
                                        <i class="fas fa-upload"></i>
                                        Upload New Photo
                                    </label>
                                    <input type="file" id="profilePictureUploadSettings" accept="image/*" class="hidden"
                                        onchange="window.handleProfilePictureSettings(event)">
                                    @if($profilePicture)
                                        <button onclick="recropProfilePictureSettings()"
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                                            <i class="fas fa-crop"></i>
                                            Recrop
                                        </button>
                                        <button onclick="removeProfilePictureSettings()"
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                                            <i class="fas fa-trash"></i>
                                            Remove
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="mb-10">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg card-shadow p-8 max-w-9xl mx-auto">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-id-card text-xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Personal Information</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">Update your account details</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2.5">
                                    <i class="fas fa-user text-blue-500 mr-2"></i>Full Name
                                </label>
                                <input type="text" id="profileName" value="{{ $userName }}"
                                    class="w-full px-5 py-3.5 border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2.5">
                                    <i class="fas fa-envelope text-blue-500 mr-2"></i>Email Address
                                </label>
                                <input type="email" id="profileEmail" value="{{ $userEmail }}"
                                    class="w-full px-5 py-3.5 border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2.5">
                                    <i class="fas fa-phone text-blue-500 mr-2"></i>Phone Number
                                </label>
                                <input type="tel" id="profilePhone" value="{{ $userPhone }}"
                                    class="w-full px-5 py-3.5 border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2.5">
                                    <i class="fas fa-coins text-blue-500 mr-2"></i>Default Currency
                                </label>
                                <select id="profileCurrency"
                                    class="w-full px-5 py-3.5 border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="INR" {{ $userCurrency == 'INR' ? 'selected' : '' }}>₹ INR - Indian Rupee
                                    </option>
                                    <option value="USD" {{ $userCurrency == 'USD' ? 'selected' : '' }}>$ USD - US Dollar
                                    </option>
                                    <option value="EUR" {{ $userCurrency == 'EUR' ? 'selected' : '' }}>€ EUR - Euro</option>
                                    <option value="GBP" {{ $userCurrency == 'GBP' ? 'selected' : '' }}>£ GBP - British Pound
                                    </option>
                                    <option value="JPY" {{ $userCurrency == 'JPY' ? 'selected' : '' }}>¥ JPY - Japanese Yen
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- About Section -->
                        <div class="mt-4 sm:mt-6">
                            <label
                                class="block text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 sm:mb-2.5">
                                <i class="fas fa-pencil-alt text-blue-500 mr-1.5 sm:mr-2"></i>About
                            </label>
                            <textarea id="profileBio" rows="3" maxlength="500"
                                class="w-full px-3 sm:px-5 py-2.5 sm:py-3.5 text-sm sm:text-base border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                placeholder="Write a short bio about yourself... (max 500 characters)">{{ $user->bio ?? '' }}</textarea>
                            <div class="mt-1.5 sm:mt-2 flex items-center justify-between">
                                <span class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">
                                    <span id="bioCharCount">{{ strlen($user->bio ?? '') }}</span>/500 characters
                                </span>
                            </div>
                        </div>

                        <div
                            class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-4 sm:pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                            <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2 flex-shrink-0"></i>
                                <span>Changes will be saved to your account</span>
                            </div>
                            <button id="saveProfileBtn" onclick="updateProfile()"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-3 px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-bold text-sm sm:text-base rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none">
                                <i id="saveProfileIcon" class="fas fa-save text-base sm:text-lg"></i>
                                <span id="saveProfileText">Save Changes</span>
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    function upiData() {
                        return {
                            upis: [],
                            loading: true,
                            showModal: false,
                            showQrModal: false,
                            editingUpi: null,
                            viewingUpi: null,
                            saving: false,
                            qrPreview: null,
                            qrFile: null,
                            uploadingForUpiId: null,
                            isDragging: false,
                            form: { name: '', upi_id: '', is_primary: false },

                            previewQr(event) {
                                const file = event.target.files[0];
                                if (file) {
                                    this.processQrFile(file);
                                }
                            },

                            async fetchUpis() {
                                this.loading = true;
                                try {
                                    const response = await fetch('/settings/upi');
                                    const data = await response.json();
                                    if (data.success) this.upis = data.upis || [];
                                } catch (e) { console.error(e); }
                                this.loading = false;
                            },

                            openAddModal() {
                                this.editingUpi = null;
                                this.form = { name: '', upi_id: '', is_primary: false };
                                this.qrPreview = null;
                                this.qrFile = null;
                                this.showModal = true;
                            },

                            openEditModal(upi) {
                                this.editingUpi = upi;
                                this.form = { name: upi.name, upi_id: upi.upi_id, is_primary: upi.is_primary };
                                this.qrPreview = upi.qr_code_url;
                                this.qrFile = null;
                                this.showModal = true;
                            },

                            closeModal() {
                                this.showModal = false;
                                this.editingUpi = null;
                                this.qrPreview = null;
                                this.qrFile = null;
                                this.isDragging = false;
                            },

                            handleQrCodeChange(event) {
                                const file = event.target.files[0];
                                if (file) {
                                    this.processQrFile(file);
                                }
                            },

                            processQrFile(file) {
                                if (!file.type.startsWith('image/')) {
                                    this.showToast('Please upload an image file', 'error');
                                    return;
                                }
                                if (file.size > 2 * 1024 * 1024) {
                                    this.showToast('File size must be less than 2MB', 'error');
                                    return;
                                }
                                this.qrFile = file;
                                const reader = new FileReader();
                                reader.onload = (e) => { this.qrPreview = e.target.result; };
                                reader.readAsDataURL(file);
                            },

                            handleDragOver(event) {
                                event.preventDefault();
                                this.isDragging = true;
                            },

                            handleDragLeave(event) {
                                event.preventDefault();
                                this.isDragging = false;
                            },

                            handleDrop(event) {
                                event.preventDefault();
                                this.isDragging = false;
                                const files = event.dataTransfer.files;
                                if (files.length > 0) {
                                    this.processQrFile(files[0]);
                                }
                            },

                            async saveUpi() {
                                this.saving = true;
                                try {
                                    const formData = new FormData();
                                    formData.append('name', this.form.name);
                                    formData.append('upi_id', this.form.upi_id);
                                    formData.append('is_primary', this.form.is_primary ? '1' : '0');
                                    if (this.qrFile) formData.append('qr_code', this.qrFile);

                                    let url = '/settings/upi';
                                    if (this.editingUpi) {
                                        formData.append('_method', 'PUT');
                                        url = '/settings/upi/' + this.editingUpi.id;
                                    }

                                    const response = await fetch(url, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Accept': 'application/json'
                                        },
                                        body: formData
                                    });
                                    const data = await response.json();
                                    if (data.success) {
                                        if (typeof showToast === 'function') showToast({ message: this.editingUpi ? 'UPI updated!' : 'UPI added!', type: 'success' });
                                        this.closeModal();
                                        this.fetchUpis();
                                    } else {
                                        if (typeof showToast === 'function') showToast({ message: data.message || 'Failed to save', type: 'error' });
                                    }
                                } catch (e) { console.error(e); }
                                this.saving = false;
                            },

                            async deleteUpi(id, name) {
                                if (!confirm('Delete "' + name + '"?')) return;
                                try {
                                    const response = await fetch('/settings/upi/' + id, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Accept': 'application/json'
                                        }
                                    });
                                    const data = await response.json();
                                    if (data.success) {
                                        if (typeof showToast === 'function') showToast({ message: 'UPI deleted!', type: 'success' });
                                        this.fetchUpis();
                                    }
                                } catch (e) { console.error(e); }
                            },

                            async setPrimary(id) {
                                try {
                                    const response = await fetch('/settings/upi/' + id + '/set-primary', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Accept': 'application/json'
                                        }
                                    });
                                    const data = await response.json();
                                    if (data.success) {
                                        if (typeof showToast === 'function') showToast({ message: 'Primary UPI updated!', type: 'success' });
                                        this.fetchUpis();
                                    }
                                } catch (e) { console.error(e); }
                            },

                            copyUpiId(upiId) {
                                navigator.clipboard.writeText(upiId).then(() => {
                                    if (typeof showToast === 'function') showToast({ message: 'Copied!', type: 'success' });
                                });
                            },

                            viewQrCode(upi) {
                                this.viewingUpi = upi;
                                this.showQrModal = true;
                            },

                            uploadQrForUpi(id) {
                                this.uploadingForUpiId = id;
                                this.$refs.qrUploadInput.click();
                            },

                            async handleQrUpload(event) {
                                const file = event.target.files[0];
                                if (!file || !this.uploadingForUpiId) return;
                                const formData = new FormData();
                                formData.append('qr_code', file);
                                formData.append('_method', 'PUT');
                                try {
                                    const response = await fetch('/settings/upi/' + this.uploadingForUpiId, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Accept': 'application/json'
                                        },
                                        body: formData
                                    });
                                    const data = await response.json();
                                    if (data.success) {
                                        if (typeof showToast === 'function') showToast({ message: 'QR uploaded!', type: 'success' });
                                        this.fetchUpis();
                                    }
                                } catch (e) { console.error(e); }
                                this.uploadingForUpiId = null;
                                event.target.value = '';
                            }
                        };
                    }
                </script>

                <!-- UPI Section (Profile) -->
                <div class="mb-10" x-data="upiData()" x-init="fetchUpis()">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg card-shadow p-4 sm:p-6 lg:p-8 max-w-9xl mx-auto">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                            <div class="flex items-center gap-3 sm:gap-4">
                                <div
                                    class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-qrcode text-lg sm:text-xl text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">UPI & QR Codes
                                    </h3>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-0.5">Add your UPI IDs
                                        and QR codes
                                        for easy payments</p>
                                </div>
                            </div>
                            <button @click="openAddModal()"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:px-5 sm:py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl transition-colors text-sm sm:text-base w-full sm:w-auto">
                                <i class="fas fa-plus"></i>
                                <span>Add UPI</span>
                            </button>
                        </div>

                        <!-- UPI List -->
                        <div class="space-y-4">
                            <!-- Loading State -->
                            <template x-if="loading">
                                <div class="flex justify-center items-center py-8">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                                </div>
                            </template>

                            <!-- Empty State -->
                            <template x-if="!loading && upis.length === 0">
                                <div
                                    class="text-center py-6 sm:py-8 bg-gray-50 dark:bg-gray-700/30 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                    <i class="fas fa-qrcode text-3xl sm:text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                    <h4 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-1">No UPI
                                        Added</h4>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-4 px-4">Add your UPI
                                        IDs for easy
                                        sharing</p>
                                    <button @click="openAddModal()"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors text-sm">
                                        <i class="fas fa-plus"></i>
                                        Add Your First UPI
                                    </button>
                                </div>
                            </template>

                            <!-- UPI Cards Grid -->
                            <template x-if="!loading && upis.length > 0">
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                                    <template x-for="upi in upis" :key="upi.id">
                                        <div
                                            class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-600 p-3 sm:p-4 relative group hover:shadow-md transition-shadow">
                                            <!-- Primary Badge -->
                                            <template x-if="upi.is_primary">
                                                <div
                                                    class="absolute -top-2 -right-2 bg-green-500 text-white text-[10px] sm:text-xs font-bold px-2 py-0.5 rounded-full shadow">
                                                    <i class="fas fa-star mr-1"></i>Primary
                                                </div>
                                            </template>

                                            <div class="flex items-start gap-3 sm:gap-4">
                                                <!-- QR Code Thumbnail -->
                                                <div class="flex-shrink-0">
                                                    <template x-if="upi.qr_code_url">
                                                        <img :src="upi.qr_code_url" :alt="upi.name"
                                                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg border-2 border-white dark:border-gray-600 shadow object-cover cursor-pointer hover:scale-105 transition-transform"
                                                            @click="viewQrCode(upi)">
                                                    </template>
                                                    <template x-if="!upi.qr_code_url">
                                                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 dark:bg-gray-600 rounded-lg flex flex-col items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-500 cursor-pointer hover:border-purple-500 transition-colors"
                                                            @click="uploadQrForUpi(upi.id)">
                                                            <i
                                                                class="fas fa-qrcode text-xl sm:text-2xl text-gray-400 dark:text-gray-500 mb-1"></i>
                                                            <span
                                                                class="text-[10px] sm:text-xs text-purple-600 dark:text-purple-400">Upload</span>
                                                        </div>
                                                    </template>
                                                </div>

                                                <!-- UPI Details -->
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-bold text-sm sm:text-base text-gray-900 dark:text-white truncate"
                                                        x-text="upi.name"></h4>
                                                    <div class="flex items-center gap-1.5 sm:gap-2 mt-1">
                                                        <span
                                                            class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 font-mono bg-gray-200 dark:bg-gray-600 px-1.5 sm:px-2 py-0.5 rounded truncate max-w-[100px] sm:max-w-[150px]"
                                                            x-text="upi.upi_id"></span>
                                                        <button @click="copyUpiId(upi.upi_id)"
                                                            class="text-gray-500 hover:text-purple-600 dark:hover:text-purple-400 flex-shrink-0 p-1">
                                                            <i class="fas fa-copy text-xs sm:text-sm"></i>
                                                        </button>
                                                    </div>

                                                    <!-- Actions -->
                                                    <div
                                                        class="flex flex-wrap items-center gap-1.5 sm:gap-2 mt-2.5 sm:mt-3">
                                                        <template x-if="!upi.is_primary">
                                                            <button @click="setPrimary(upi.id)"
                                                                class="px-2 sm:px-2.5 py-1 sm:py-1.5 text-[10px] sm:text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-md hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors font-medium">
                                                                <i class="fas fa-star mr-0.5 sm:mr-1"></i>Primary
                                                            </button>
                                                        </template>
                                                        <button @click="openEditModal(upi)"
                                                            class="px-2 sm:px-2.5 py-1 sm:py-1.5 text-[10px] sm:text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-md hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors font-medium">
                                                            <i class="fas fa-edit mr-0.5 sm:mr-1"></i>Edit
                                                        </button>
                                                        <button @click="deleteUpi(upi.id, upi.name)"
                                                            class="px-2 sm:px-2.5 py-1 sm:py-1.5 text-[10px] sm:text-xs bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-md hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors font-medium">
                                                            <i class="fas fa-trash mr-0.5 sm:mr-1"></i>Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Hidden file input for QR upload -->
                        <input type="file" x-ref="qrUploadInput" @change="handleQrUpload" accept="image/*" class="hidden">

                        <!-- Add/Edit UPI Modal -->
                        <div x-show="showModal" x-cloak
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            @click.self="closeModal()">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white"
                                        x-text="editingUpi ? 'Edit UPI' : 'Add New UPI'"></h3>
                                    <button @click="closeModal()"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>

                                <form @submit.prevent="saveUpi()" enctype="multipart/form-data">
                                    <!-- Name -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fas fa-tag mr-1"></i>Label/Name
                                        </label>
                                        <input type="text" x-model="form.name" required
                                            placeholder="e.g., Personal, Business, GPay"
                                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors text-gray-900 dark:text-white">
                                    </div>

                                    <!-- UPI ID -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fas fa-at mr-1"></i>UPI ID
                                        </label>
                                        <input type="text" x-model="form.upi_id" required placeholder="yourname@upi"
                                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors text-gray-900 dark:text-white">
                                    </div>

                                    <!-- QR Code Upload -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fas fa-qrcode mr-1"></i>QR Code (Optional)
                                        </label>
                                        <div class="relative" @dragover.prevent="isDragging = true"
                                            @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop($event)">
                                            <input type="file" x-ref="qrInput" @change="previewQr" accept="image/*"
                                                class="hidden">
                                            <div @click="$refs.qrInput.click()"
                                                class="cursor-pointer border-2 border-dashed rounded-xl p-6 text-center transition-all"
                                                :class="isDragging ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-purple-400 dark:hover:border-purple-500 bg-gray-50 dark:bg-gray-700/50'">
                                                <template x-if="qrPreview">
                                                    <div class="flex flex-col items-center">
                                                        <img :src="qrPreview"
                                                            class="w-32 h-32 rounded-lg object-cover border-2 border-gray-200 dark:border-gray-600 mb-3">
                                                        <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">
                                                            Click or drag to replace</p>
                                                    </div>
                                                </template>
                                                <template x-if="!qrPreview">
                                                    <div class="flex flex-col items-center">
                                                        <div
                                                            class="w-16 h-16 bg-gray-100 dark:bg-gray-600 rounded-xl flex items-center justify-center mb-3">
                                                            <i
                                                                class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-gray-500"></i>
                                                        </div>
                                                        <p
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Click to upload or drag and drop</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG up to
                                                            2MB</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Primary Checkbox -->
                                    <div class="mb-6">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="checkbox" x-model="form.is_primary"
                                                class="w-5 h-5 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Set as primary UPI</span>
                                        </label>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="flex gap-3">
                                        <button type="button" @click="closeModal()"
                                            class="flex-1 px-4 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit" :disabled="saving"
                                            class="flex-1 px-4 py-3 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-400 text-white font-semibold rounded-xl transition-colors">
                                            <span x-show="!saving" x-text="editingUpi ? 'Update UPI' : 'Add UPI'"></span>
                                            <span x-show="saving"><i
                                                    class="fas fa-spinner fa-spin mr-2"></i>Saving...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- View QR Modal -->
                        <div x-show="showQrModal" x-cloak
                            class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-4"
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            @click.self="showQrModal = false">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4" x-text="viewingUpi?.name">
                                </h3>
                                <img :src="viewingUpi?.qr_code_url"
                                    class="w-64 h-64 mx-auto rounded-xl border-4 border-gray-200 dark:border-gray-600">
                                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400 font-mono"
                                    x-text="viewingUpi?.upi_id"></p>
                                <button @click="showQrModal = false"
                                    class="mt-4 px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl transition-colors">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <!-- End Profile Card Wrapper -->

            <script>
                // Profile Update - Define immediately so it's available for onclick
                window.updateProfile = async function () {
                    const btn = document.getElementById('saveProfileBtn');
                    const icon = document.getElementById('saveProfileIcon');
                    const text = document.getElementById('saveProfileText');

                    const name = document.getElementById('profileName').value;
                    const email = document.getElementById('profileEmail').value;
                    const phone = document.getElementById('profilePhone').value;
                    const currency = document.getElementById('profileCurrency').value;
                    const bio = document.getElementById('profileBio').value;

                    // Validation
                    if (!name || !email) {
                        if (typeof popupError === 'function') {
                            popupError('Please fill in all required fields', 'Validation Error');
                        } else {
                            alert('Please fill in all required fields');
                        }
                        return;
                    }

                    // Validate email format
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        if (typeof popupError === 'function') {
                            popupError('Please enter a valid email address', 'Validation Error');
                        } else {
                            alert('Please enter a valid email address');
                        }
                        return;
                    }

                    // Show loading state
                    btn.disabled = true;
                    icon.className = 'fas fa-spinner fa-spin text-lg';
                    text.textContent = 'Saving...';

                    try {
                        const response = await fetch('/settings/profile', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ name, email, phone, currency, bio })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            // Show success state
                            icon.className = 'fas fa-check text-lg';
                            text.textContent = 'Saved!';

                            if (typeof popupSuccess === 'function') {
                                popupSuccess(data.message || 'Profile updated successfully', 'Success');
                            } else {
                                alert(data.message || 'Profile updated successfully');
                            }
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            // Reset button state on failed response
                            btn.disabled = false;
                            icon.className = 'fas fa-save text-lg';
                            text.textContent = 'Save Changes';

                            if (typeof popupError === 'function') {
                                popupError(data.message || 'Failed to update profile', 'Error');
                            } else {
                                alert(data.message || 'Failed to update profile');
                            }
                        }
                    } catch (error) {
                        // Reset button state on error
                        btn.disabled = false;
                        icon.className = 'fas fa-save text-lg';
                        text.textContent = 'Save Changes';

                        if (typeof popupError === 'function') {
                            popupError('Failed to update profile. Please try again.', 'Error');
                        } else {
                            alert('Failed to update profile. Please try again.');
                        }
                    }
                };

                // Bio character counter
                document.addEventListener('DOMContentLoaded', function () {
                    const bioTextarea = document.getElementById('profileBio');
                    const bioCharCount = document.getElementById('bioCharCount');

                    if (bioTextarea && bioCharCount) {
                        bioTextarea.addEventListener('input', function () {
                            bioCharCount.textContent = this.value.length;
                        });
                    }
                });
            </script>

        </div>
        <!-- End Profile Tab Content -->

        <!-- Security Tab Content -->
        <div x-show="activeTab === 'security'" x-cloak>
            <!-- Security Settings Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-shield-alt text-2xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Security Settings</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage your account security and
                            authentication</p>
                    </div>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="mb-10" style="isolation: isolate;">
                <div class="bg-white/80 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-xl overflow-hidden border border-white/60 dark:border-gray-700 max-w-9xl mx-auto"
                    style="position: relative; z-index: 1;">
                    <!-- Header with gradient background -->
                    <div class="px-8 py-6"
                        style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%); position: relative; z-index: 2;">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-14 h-14 bg-white/25 backdrop-blur-md rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg border border-white/30">
                                <i class="fas fa-shield-alt text-2xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Change Password</h3>
                                <p class="text-sm text-white/80 mt-0.5">Keep your account secure with a strong password</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <!-- Security Tips Banner -->
                        <div
                            class="mb-8 p-4 bg-gradient-to-r from-blue-50/90 to-indigo-50/90 dark:from-gray-700/80 dark:to-gray-700/80 backdrop-blur-sm rounded-xl border border-blue-200/50 dark:border-gray-600 shadow-sm">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <i class="fas fa-lightbulb text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-blue-900 dark:text-blue-300 text-sm">Security Tip</h4>
                                    <p class="text-xs text-blue-700 dark:text-blue-400 mt-1">Use a mix of uppercase,
                                        lowercase, numbers, and special characters for a stronger password.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Current Password Field -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2.5">
                                    <span class="inline-flex items-center gap-2">
                                        <span
                                            class="w-7 h-7 bg-gradient-to-br from-indigo-100 to-purple-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center shadow-sm">
                                            <i class="fas fa-lock text-sm text-indigo-600 dark:text-indigo-400"></i>
                                        </span>
                                        Current Password
                                    </span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="currentPassword" placeholder="Enter current password"
                                        class="w-full px-5 py-4 pr-12 bg-white/90 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm hover:border-indigo-300 dark:hover:border-indigo-500"
                                        oninput="autoCheckCurrentPassword()">
                                    <script>
                                        // Track password verification status
                                        window.currentPasswordVerified = false;

                                        async function autoCheckCurrentPassword() {
                                            const passwordField = document.getElementById('currentPassword');
                                            const loadingIndicator = document.getElementById('passwordLoadingIndicator');
                                            const errorMsg = document.getElementById('currentPasswordError');
                                            const sendOtpSection = document.getElementById('sendOtpSection');
                                            const otpSection = document.getElementById('otpInputSection');
                                            const newPassword = document.getElementById('newPassword');
                                            const confirmPassword = document.getElementById('confirmPassword');
                                            const passwordVerifiedTick = document.getElementById('passwordVerifiedTick');

                                            errorMsg.classList.add('hidden');
                                            sendOtpSection.classList.add('hidden');
                                            otpSection.classList.add('hidden');
                                            passwordVerifiedTick.classList.add('hidden');
                                            newPassword.disabled = true;
                                            confirmPassword.disabled = true;
                                            window.currentPasswordVerified = false;

                                            const currentPassword = passwordField.value;
                                            if (!currentPassword || currentPassword.length < 6) {
                                                loadingIndicator.classList.add('hidden');
                                                return;
                                            }

                                            loadingIndicator.classList.remove('hidden');

                                            // Debounce to avoid too many requests
                                            if (window._currentPasswordTimeout) clearTimeout(window._currentPasswordTimeout);
                                            window._currentPasswordTimeout = setTimeout(async () => {
                                                try {
                                                    const response = await fetch('/settings/verify-current-password', {
                                                        method: 'POST',
                                                        credentials: 'same-origin',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                            'Accept': 'application/json'
                                                        },
                                                        body: JSON.stringify({ current_password: currentPassword })
                                                    });
                                                    const data = await response.json();
                                                    loadingIndicator.classList.add('hidden');
                                                    if (response.ok && data.success) {
                                                        window.currentPasswordVerified = true;
                                                        passwordVerifiedTick.classList.remove('hidden');
                                                        sendOtpSection.classList.remove('hidden');
                                                        errorMsg.classList.add('hidden');
                                                    } else {
                                                        const errorText = errorMsg.querySelector('#currentPasswordErrorText') || errorMsg;
                                                        errorText.textContent = data.message || 'Current password is incorrect';
                                                        errorMsg.classList.remove('hidden');
                                                    }
                                                } catch (error) {
                                                    loadingIndicator.classList.add('hidden');
                                                    const errorText = errorMsg.querySelector('#currentPasswordErrorText') || errorMsg;
                                                    errorText.textContent = 'Error verifying password. Please try again.';
                                                    errorMsg.classList.remove('hidden');
                                                }
                                            }, 700); // 700ms debounce
                                        }

                                        // Send OTP function
                                        window.sendPasswordOtp = async function () {
                                            const currentPassword = document.getElementById('currentPassword').value;
                                            const sendOtpBtn = document.getElementById('sendOtpBtn');
                                            const sendOtpBtnText = document.getElementById('sendOtpBtnText');
                                            const sendOtpBtnLoading = document.getElementById('sendOtpBtnLoading');
                                            const sendOtpSection = document.getElementById('sendOtpSection');
                                            const otpSection = document.getElementById('otpInputSection');
                                            const otpSentSuccess = document.getElementById('otpSentSuccess');

                                            if (!window.currentPasswordVerified) {
                                                if (typeof popupError === 'function') {
                                                    popupError('Please verify your current password first', 'Error');
                                                }
                                                return;
                                            }

                                            // Show loading state
                                            sendOtpBtn.disabled = true;
                                            sendOtpBtnText.classList.add('hidden');
                                            sendOtpBtnLoading.classList.remove('hidden');

                                            try {
                                                // Use a temporary valid password for OTP request
                                                const tempPassword = 'TempPass' + Math.floor(Math.random() * 10000000);

                                                const response = await fetch('/settings/send-password-otp', {
                                                    method: 'POST',
                                                    credentials: 'same-origin',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                        'Accept': 'application/json'
                                                    },
                                                    body: JSON.stringify({
                                                        current_password: currentPassword,
                                                        new_password: tempPassword,
                                                        new_password_confirmation: tempPassword
                                                    })
                                                });
                                                const data = await response.json();

                                                // Reset button state
                                                sendOtpBtn.disabled = false;
                                                sendOtpBtnText.classList.remove('hidden');
                                                sendOtpBtnLoading.classList.add('hidden');

                                                if (response.ok && data.success) {
                                                    // Show success message briefly
                                                    otpSentSuccess.classList.remove('hidden');

                                                    // After 1.5 seconds, hide send button section and show OTP input
                                                    setTimeout(() => {
                                                        sendOtpSection.classList.add('hidden');
                                                        otpSection.classList.remove('hidden');
                                                    }, 1500);

                                                    // Show toast notification
                                                    if (typeof toastOtp === 'function') {
                                                        toastOtp('{{ auth()->user()->email ?? "your email" }}');
                                                    }

                                                    // Add OTP to notification section
                                                    if (window.addOtpNotification) {
                                                        window.addOtpNotification(data.debug_otp || 'OTP sent to your email');
                                                    }
                                                } else {
                                                    if (typeof popupError === 'function') {
                                                        popupError(data.message || 'Failed to send OTP. Please try again.', 'Error');
                                                    }
                                                }
                                            } catch (error) {
                                                // Reset button state
                                                sendOtpBtn.disabled = false;
                                                sendOtpBtnText.classList.remove('hidden');
                                                sendOtpBtnLoading.classList.add('hidden');

                                                if (typeof popupError === 'function') {
                                                    popupError('Failed to send OTP. Please try again.', 'Error');
                                                }
                                            }
                                        };

                                        // Add OTP notification to website notification section with 1-min overview
                                        window.addOtpNotification = function (otp) {
                                            if (!window.otpNotificationTimer) {
                                                window.otpNotificationTimer = null;
                                            }
                                            let countdown = 60;
                                            const notification = {
                                                id: 'otp-' + Date.now(),
                                                title: 'Password Change OTP',
                                                message: 'Your OTP is: ' + otp + ' (valid for 10 min)',
                                                icon: 'fa-key',
                                                color: 'primary',
                                                created_at: new Date().toISOString(),
                                                is_read: false,
                                                overview: 'OTP will disappear in 1 min',
                                                countdown: countdown
                                            };
                                            // Push to notificationsDropdown if available
                                            if (window.notificationsDropdownInstance && typeof window.notificationsDropdownInstance.notifications === 'object') {
                                                window.notificationsDropdownInstance.notifications.unshift(notification);
                                            }
                                            // Show overview countdown
                                            const interval = setInterval(() => {
                                                countdown--;
                                                notification.overview = 'OTP will disappear in ' + countdown + 's';
                                                if (countdown <= 0) {
                                                    clearInterval(interval);
                                                    // Remove notification after 1 min
                                                    if (window.notificationsDropdownInstance && typeof window.notificationsDropdownInstance.notifications === 'object') {
                                                        window.notificationsDropdownInstance.notifications = window.notificationsDropdownInstance.notifications.filter(n => n.id !== notification.id);
                                                    }
                                                }
                                            }, 1000);
                                        };

                                        // Forgot Password Functions
                                        window.forgotPasswordOtpVerified = false;
                                        window.forgotPasswordEmail = '{{ auth()->user()->email ?? session("user_email") }}';

                                        window.toggleForgotPasswordSection = function () {
                                            const section = document.getElementById('forgotPasswordSection');
                                            const sendOtpSection = document.getElementById('sendOtpSection');
                                            const otpInputSection = document.getElementById('otpInputSection');

                                            // Hide other sections
                                            sendOtpSection.classList.add('hidden');
                                            otpInputSection.classList.add('hidden');

                                            // Toggle forgot password section
                                            section.classList.toggle('hidden');

                                            // Reset to step 1
                                            document.getElementById('forgotStep1').classList.remove('hidden');
                                            document.getElementById('forgotStep2').classList.add('hidden');
                                            document.getElementById('forgotStep3').classList.add('hidden');
                                        };

                                        window.closeForgotPasswordSection = function () {
                                            document.getElementById('forgotPasswordSection').classList.add('hidden');
                                        };

                                        window.sendForgotPasswordOtp = async function () {
                                            const btn = document.getElementById('sendForgotOtpBtn');
                                            const btnText = document.getElementById('sendForgotOtpBtnText');
                                            const btnLoading = document.getElementById('sendForgotOtpBtnLoading');

                                            btn.disabled = true;
                                            btnText.classList.add('hidden');
                                            btnLoading.classList.remove('hidden');

                                            try {
                                                const response = await fetch('/forgot-password', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                        'Accept': 'application/json'
                                                    },
                                                    body: JSON.stringify({ email: window.forgotPasswordEmail })
                                                });

                                                const data = await response.json();

                                                btn.disabled = false;
                                                btnText.classList.remove('hidden');
                                                btnLoading.classList.add('hidden');

                                                if (data.success) {
                                                    if (typeof popupSuccess === 'function') {
                                                        popupSuccess('OTP sent to your email!', 'Success');
                                                    }
                                                    // Move to step 2
                                                    document.getElementById('forgotStep1').classList.add('hidden');
                                                    document.getElementById('forgotStep2').classList.remove('hidden');
                                                } else {
                                                    if (typeof popupError === 'function') {
                                                        popupError(data.message || 'Failed to send OTP', 'Error');
                                                    }
                                                }
                                            } catch (error) {
                                                btn.disabled = false;
                                                btnText.classList.remove('hidden');
                                                btnLoading.classList.add('hidden');
                                                if (typeof popupError === 'function') {
                                                    popupError('Failed to send OTP. Please try again.', 'Error');
                                                }
                                            }
                                        };

                                        window.backToForgotStep1 = function () {
                                            document.getElementById('forgotStep1').classList.remove('hidden');
                                            document.getElementById('forgotStep2').classList.add('hidden');
                                            document.getElementById('forgotOtpInput').value = '';
                                            document.getElementById('forgotOtpError').classList.add('hidden');
                                        };

                                        window.verifyForgotOtp = async function () {
                                            const otp = document.getElementById('forgotOtpInput').value;
                                            const errorEl = document.getElementById('forgotOtpError');

                                            if (!otp || otp.length !== 8) {
                                                errorEl.textContent = 'Please enter a valid 8-digit OTP';
                                                errorEl.classList.remove('hidden');
                                                return;
                                            }

                                            try {
                                                const response = await fetch('/verify-otp', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                        'Accept': 'application/json'
                                                    },
                                                    body: JSON.stringify({
                                                        email: window.forgotPasswordEmail,
                                                        otp: otp
                                                    })
                                                });

                                                const data = await response.json();

                                                if (data.success) {
                                                    window.forgotPasswordOtpVerified = true;
                                                    window.forgotPasswordOtp = otp;
                                                    errorEl.classList.add('hidden');
                                                    if (typeof popupSuccess === 'function') {
                                                        popupSuccess('OTP verified! Now set your new password.', 'Success');
                                                    }
                                                    // Move to step 3
                                                    document.getElementById('forgotStep2').classList.add('hidden');
                                                    document.getElementById('forgotStep3').classList.remove('hidden');
                                                } else {
                                                    errorEl.textContent = data.message || 'Invalid OTP';
                                                    errorEl.classList.remove('hidden');
                                                }
                                            } catch (error) {
                                                errorEl.textContent = 'Failed to verify OTP. Please try again.';
                                                errorEl.classList.remove('hidden');
                                            }
                                        };

                                        window.resetForgotPassword = async function () {
                                            const newPassword = document.getElementById('forgotNewPassword').value;
                                            const confirmPassword = document.getElementById('forgotConfirmPassword').value;
                                            const errorEl = document.getElementById('forgotPasswordError');
                                            const btn = document.getElementById('resetForgotPasswordBtn');
                                            const btnText = document.getElementById('resetForgotPasswordBtnText');
                                            const btnLoading = document.getElementById('resetForgotPasswordBtnLoading');

                                            errorEl.classList.add('hidden');

                                            if (!newPassword || newPassword.length < 8) {
                                                errorEl.textContent = 'Password must be at least 8 characters';
                                                errorEl.classList.remove('hidden');
                                                return;
                                            }

                                            if (newPassword !== confirmPassword) {
                                                errorEl.textContent = 'Passwords do not match';
                                                errorEl.classList.remove('hidden');
                                                return;
                                            }

                                            btn.disabled = true;
                                            btnText.classList.add('hidden');
                                            btnLoading.classList.remove('hidden');

                                            try {
                                                const response = await fetch('/reset-password', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                        'Accept': 'application/json'
                                                    },
                                                    body: JSON.stringify({
                                                        email: window.forgotPasswordEmail,
                                                        otp: window.forgotPasswordOtp,
                                                        password: newPassword,
                                                        password_confirmation: confirmPassword
                                                    })
                                                });

                                                const data = await response.json();

                                                btn.disabled = false;
                                                btnText.classList.remove('hidden');
                                                btnLoading.classList.add('hidden');

                                                if (data.success) {
                                                    if (typeof popupSuccess === 'function') {
                                                        popupSuccess('Password reset successful!', 'Success');
                                                    }
                                                    // Close the forgot password section and reset form
                                                    document.getElementById('forgotPasswordSection').classList.add('hidden');
                                                    document.getElementById('forgotNewPassword').value = '';
                                                    document.getElementById('forgotConfirmPassword').value = '';
                                                    document.getElementById('forgotOtpInput').value = '';
                                                    document.getElementById('currentPassword').value = '';
                                                    // Reset to step 1 for next time
                                                    document.getElementById('forgotStep1').classList.remove('hidden');
                                                    document.getElementById('forgotStep2').classList.add('hidden');
                                                    document.getElementById('forgotStep3').classList.add('hidden');
                                                } else {
                                                    errorEl.textContent = data.message || 'Failed to reset password';
                                                    errorEl.classList.remove('hidden');
                                                }
                                            } catch (error) {
                                                btn.disabled = false;
                                                btnText.classList.remove('hidden');
                                                btnLoading.classList.add('hidden');
                                                errorEl.textContent = 'Failed to reset password. Please try again.';
                                                errorEl.classList.remove('hidden');
                                            }
                                        };
                                    </script>
                                    <!-- Password Verified Tick -->
                                    <span id="passwordVerifiedTick"
                                        class="hidden absolute right-12 top-1/2 -translate-y-1/2 text-green-500 text-xl z-20">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    <button type="button" onclick="window.togglePasswordVisibility('currentPassword')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors z-20">
                                        <i class="fas fa-eye text-lg" id="currentPassword-eye"></i>
                                    </button>
                                    <script>
                                        // Toggle password visibility function
                                        window.togglePasswordVisibility = function (fieldId) {
                                            const input = document.getElementById(fieldId);
                                            const icon = document.getElementById(fieldId + '-eye');
                                            if (!input || !icon) return;

                                            if (input.type === 'password') {
                                                input.type = 'text';
                                                icon.classList.remove('fa-eye');
                                                icon.classList.add('fa-eye-slash');
                                            } else {
                                                input.type = 'password';
                                                icon.classList.remove('fa-eye-slash');
                                                icon.classList.add('fa-eye');
                                            }
                                        };
                                    </script>
                                    <div id="passwordLoadingIndicator"
                                        class="hidden absolute inset-0 bg-white/95 dark:bg-gray-700/95 rounded-xl flex items-center justify-center pointer-events-none z-10 backdrop-blur-sm border-2 border-indigo-200 dark:border-indigo-600">
                                        <div class="flex items-center gap-3 text-indigo-600 dark:text-indigo-400">
                                            <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <span class="text-sm font-semibold">Verifying...</span>
                                        </div>
                                    </div>
                                </div>
                                <p id="currentPasswordError"
                                    class="hidden text-red-600 dark:text-red-400 text-sm mt-3 flex items-center gap-2 bg-red-50 dark:bg-red-900/20 px-4 py-2.5 rounded-xl border border-red-200 dark:border-red-800">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span id="currentPasswordErrorText"></span>
                                </p>
                                <!-- Forgot Password Link -->
                                <div class="mt-4">
                                    <button type="button" onclick="toggleForgotPasswordSection()"
                                        class="group inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium transition-all bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 px-4 py-2.5 rounded-xl">
                                        <i class="fas fa-question-circle group-hover:rotate-12 transition-transform"></i>
                                        <span>Forgot your current password?</span>
                                        <i
                                            class="fas fa-arrow-right text-xs opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Forgot Password Section (Hidden by default) -->
                            <div id="forgotPasswordSection" class="hidden">
                                <div
                                    class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-2 border-amber-200 dark:border-amber-700 rounded-xl p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                                                <i class="fas fa-key text-xl text-amber-600 dark:text-amber-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-amber-800 dark:text-amber-300">Reset
                                                    Password via Email</h4>
                                                <p class="text-sm text-amber-600 dark:text-amber-400">We'll send an OTP to
                                                    your
                                                    registered email</p>
                                            </div>
                                        </div>
                                        <button onclick="closeForgotPasswordSection()"
                                            class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">
                                            <i class="fas fa-times text-xl"></i>
                                        </button>
                                    </div>

                                    <!-- Step 1: Send OTP -->
                                    <div id="forgotStep1">
                                        <div class="mb-4 p-3 bg-amber-100 dark:bg-amber-900/40 rounded-lg">
                                            <div class="flex items-center gap-2 text-amber-700 dark:text-amber-300 text-sm">
                                                <i class="fas fa-envelope"></i>
                                                <span>OTP will be sent to:
                                                    <strong>{{ auth()->user()->email ?? session('user_email') }}</strong></span>
                                            </div>
                                        </div>
                                        <button onclick="sendForgotPasswordOtp()" id="sendForgotOtpBtn"
                                            class="w-full inline-flex items-center justify-center gap-3 px-6 py-3.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                            <span id="sendForgotOtpBtnText" class="flex items-center gap-2">
                                                <i class="fas fa-paper-plane"></i>
                                                Send OTP to Email
                                            </span>
                                            <span id="sendForgotOtpBtnLoading" class="hidden flex items-center gap-2">
                                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                Sending OTP...
                                            </span>
                                        </button>
                                    </div>

                                    <!-- Step 2: Verify OTP -->
                                    <div id="forgotStep2" class="hidden">
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                                <i class="fas fa-shield-check mr-2"></i>Enter 8-digit OTP
                                            </label>
                                            <div class="flex gap-3">
                                                <input type="text" id="forgotOtpInput" maxlength="8"
                                                    class="flex-1 px-5 py-3.5 border-2 border-amber-300 dark:border-amber-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-center text-xl tracking-widest font-bold transition-all"
                                                    placeholder="00000000">
                                                <button onclick="verifyForgotOtp()" id="verifyForgotOtpBtn"
                                                    class="px-6 py-3.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg transition-all">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                            <p id="forgotOtpError"
                                                class="hidden text-red-600 dark:text-red-400 text-sm mt-2">
                                            </p>
                                        </div>
                                        <button onclick="backToForgotStep1()"
                                            class="text-sm text-amber-600 dark:text-amber-400 hover:text-amber-700 flex items-center gap-2">
                                            <i class="fas fa-arrow-left"></i>
                                            <span>Resend OTP</span>
                                        </button>
                                    </div>

                                    <!-- Step 3: New Password -->
                                    <div id="forgotStep3" class="hidden">
                                        <div class="space-y-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                                    <i class="fas fa-lock mr-2"></i>New Password
                                                </label>
                                                <div class="relative">
                                                    <input type="password" id="forgotNewPassword" minlength="8"
                                                        class="w-full px-5 py-3.5 pr-12 border-2 border-amber-300 dark:border-amber-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                                        placeholder="Enter new password (min 8 characters)">
                                                    <button type="button"
                                                        onclick="window.togglePasswordVisibility('forgotNewPassword')"
                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-amber-600 dark:text-gray-400 dark:hover:text-amber-400 transition-colors">
                                                        <i class="fas fa-eye text-lg" id="forgotNewPassword-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                                    <i class="fas fa-check-double mr-2"></i>Confirm New Password
                                                </label>
                                                <div class="relative">
                                                    <input type="password" id="forgotConfirmPassword" minlength="8"
                                                        class="w-full px-5 py-3.5 pr-12 border-2 border-amber-300 dark:border-amber-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                                        placeholder="Re-enter new password">
                                                    <button type="button"
                                                        onclick="window.togglePasswordVisibility('forgotConfirmPassword')"
                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-amber-600 dark:text-gray-400 dark:hover:text-amber-400 transition-colors">
                                                        <i class="fas fa-eye text-lg" id="forgotConfirmPassword-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <p id="forgotPasswordError"
                                                class="hidden text-red-600 dark:text-red-400 text-sm">
                                            </p>
                                            <button onclick="resetForgotPassword()" id="resetForgotPasswordBtn"
                                                class="w-full inline-flex items-center justify-center gap-3 px-6 py-3.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
                                                <span id="resetForgotPasswordBtnText" class="flex items-center gap-2">
                                                    <i class="fas fa-save"></i>
                                                    Reset Password
                                                </span>
                                                <span id="resetForgotPasswordBtnLoading"
                                                    class="hidden flex items-center gap-2">
                                                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                    Updating...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Send OTP Section (Shown after password verification) -->
                            <div id="sendOtpSection" class="hidden">
                                <div
                                    class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-200 dark:border-green-700 rounded-xl p-6">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                                <i
                                                    class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-green-800 dark:text-green-300">Password
                                                Verified!</h4>
                                            <p class="text-sm text-green-600 dark:text-green-400 mt-1">Click the button to
                                                send
                                                OTP to your registered email.</p>
                                        </div>
                                        <button id="sendOtpBtn" onclick="sendPasswordOtp()"
                                            class="inline-flex items-center gap-3 px-6 py-3.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                            <span id="sendOtpBtnText" class="flex items-center gap-2">
                                                <i class="fas fa-paper-plane"></i>
                                                Send OTP
                                            </span>
                                            <span id="sendOtpBtnLoading" class="hidden flex items-center gap-2">
                                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                Sending...
                                            </span>
                                        </button>
                                    </div>
                                    <!-- Success message after OTP sent -->
                                    <div id="otpSentSuccess"
                                        class="hidden mt-4 p-3 bg-green-100 dark:bg-green-900/40 rounded-lg">
                                        <div class="flex items-center gap-2 text-green-700 dark:text-green-300">
                                            <i class="fas fa-envelope-circle-check text-xl"></i>
                                            <span class="font-medium">OTP sent successfully! Check your email.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- OTP Input Section (Hidden by default) -->
                            <script>
                                // Guarantee global scope and availability for OTP verification
                                window.verifyOtpAndEnableFields = async function verifyOtpAndEnableFields() {
                                    const otp = document.getElementById('otpInputField').value;
                                    const otpTick = document.getElementById('otpSuccessTick');
                                    const otpErrorMsg = document.getElementById('otpErrorMsg');
                                    // Hide tick and error by default
                                    otpTick.classList.add('hidden');
                                    otpErrorMsg.classList.add('hidden');
                                    otpErrorMsg.textContent = '';

                                    if (!otp || otp.length !== 8) {
                                        const errorText = otpErrorMsg.querySelector('#otpErrorMsgText') || otpErrorMsg;
                                        errorText.textContent = 'Please enter a valid 8-digit OTP';
                                        otpErrorMsg.classList.remove('hidden');
                                        return;
                                    }

                                    try {
                                        const response = await fetch('/settings/verify-otp', {
                                            method: 'POST',
                                            credentials: 'same-origin',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({ otp: otp })
                                        });

                                        const data = await response.json();

                                        if (data.success) {
                                            otpVerified = true;
                                            // Show green tick
                                            otpTick.classList.remove('hidden');
                                            // Enable new password fields
                                            const newPasswordField = document.getElementById('newPassword');
                                            const confirmPasswordField = document.getElementById('confirmPassword');
                                            newPasswordField.disabled = false;
                                            confirmPasswordField.disabled = false;
                                            newPasswordField.classList.remove('bg-gray-100', 'cursor-not-allowed');
                                            confirmPasswordField.classList.remove('bg-gray-100', 'cursor-not-allowed');
                                            newPasswordField.focus();
                                        } else {
                                            otpVerified = false;
                                            // Show custom error message for 3 seconds
                                            const errorText = otpErrorMsg.querySelector('#otpErrorMsgText') || otpErrorMsg;
                                            errorText.textContent = 'OTP not Match Put The Currect OTP !';
                                            otpErrorMsg.classList.remove('hidden');
                                            setTimeout(() => {
                                                otpErrorMsg.classList.add('hidden');
                                            }, 3000);
                                        }
                                    } catch (error) {
                                        otpVerified = false;
                                        const errorText = otpErrorMsg.querySelector('#otpErrorMsgText') || otpErrorMsg;
                                        errorText.textContent = 'Failed to verify OTP. Please try again.';
                                        otpErrorMsg.classList.remove('hidden');
                                        setTimeout(() => {
                                            otpErrorMsg.classList.add('hidden');
                                        }, 3000);
                                    }
                                };
                            </script>
                            <div id="otpInputSection" class="hidden">
                                <div
                                    class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-xl p-6">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i class="fas fa-envelope-open-text text-blue-500 mr-2"></i>Enter OTP
                                    </label>
                                    <div class="flex gap-3 items-center">
                                        <input type="text" id="otpInputField" placeholder="Enter 8-digit OTP" maxlength="8"
                                            class="flex-1 px-5 py-3.5 border-2 border-blue-300 dark:border-blue-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-lg tracking-widest font-bold transition-all">
                                        <span id="otpSuccessTick"
                                            class="hidden text-green-600 text-3xl ml-2 animate-bounce"><i
                                                class="fas fa-check-circle"></i></span>
                                        <button onclick="verifyOtpAndEnableFields()"
                                            class="inline-flex items-center gap-2 px-6 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                                            <i class="fas fa-shield-check"></i>
                                            Verify
                                        </button>
                                    </div>
                                    <p id="otpErrorMsg"
                                        class="hidden text-red-600 dark:text-red-400 text-sm mt-3 flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span id="otpErrorMsgText"></span>
                                    </p>
                                    <div class="mt-3 flex items-center gap-2 text-xs text-blue-700 dark:text-blue-300">
                                        <i class="fas fa-info-circle"></i>
                                        <span>OTP sent to your registered email. Check notification panel.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2.5">
                                    <span class="inline-flex items-center gap-2">
                                        <span
                                            class="w-7 h-7 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-key text-sm text-green-600 dark:text-green-400"></i>
                                        </span>
                                        New Password
                                    </span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="newPassword"
                                        placeholder="Enter new password (min 8 characters)" disabled
                                        class="w-full px-5 py-4 pr-12 bg-gray-50 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 dark:text-white rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white dark:focus:bg-gray-700 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 transition-all shadow-sm hover:border-green-300 dark:hover:border-green-500">
                                    <button type="button" onclick="window.togglePasswordVisibility('newPassword')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition-colors z-20">
                                        <i class="fas fa-eye text-lg" id="newPassword-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2.5">
                                    <span class="inline-flex items-center gap-2">
                                        <span
                                            class="w-7 h-7 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-check-double text-sm text-green-600 dark:text-green-400"></i>
                                        </span>
                                        Confirm New Password
                                    </span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="confirmPassword" placeholder="Re-enter new password" disabled
                                        class="w-full px-5 py-4 pr-12 bg-gray-50 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 dark:text-white rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white dark:focus:bg-gray-700 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 transition-all shadow-sm hover:border-green-300 dark:hover:border-green-500">
                                    <button type="button" onclick="window.togglePasswordVisibility('confirmPassword')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition-colors z-20">
                                        <i class="fas fa-eye text-lg" id="confirmPassword-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <script>
                            // Guarantee global scope and availability for password update
                            window.updatePassword = async function updatePassword() {
                                // Always get the current password from the input field to ensure it is sent
                                const currentPassword = document.getElementById('currentPassword').value;
                                const newPassword = document.getElementById('newPassword').value;
                                const confirmPassword = document.getElementById('confirmPassword').value;

                                if (!otpVerified) {
                                    popupError('Please verify OTP first', 'Error');
                                    return;
                                }

                                if (document.getElementById('newPassword').disabled || document.getElementById('confirmPassword').disabled) {
                                    popupError('Please verify OTP and enter new password.', 'Error');
                                    return;
                                }
                                if (!newPassword || !confirmPassword) {
                                    popupError('Please fill in both new password fields.', 'Error');
                                    return;
                                }
                                if (newPassword !== confirmPassword) {
                                    popupError('New password and confirmation do not match.', 'Error');
                                    return;
                                }
                                if (newPassword.length < 8) {
                                    popupError('Password must be at least 8 characters.', 'Error');
                                    return;
                                }

                                // Show confirmation popup
                                popupConfirm(
                                    'Are you sure you want to change your password?',
                                    'Confirm Password Change',
                                    async function () {
                                        // User clicked Confirm - Update password
                                        try {
                                            const payload = {
                                                current_password: currentPassword,
                                                new_password: newPassword,
                                                new_password_confirmation: confirmPassword
                                            };
                                            const response = await fetch('/settings/update-password', {
                                                method: 'POST',
                                                credentials: 'same-origin',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify(payload)
                                            });

                                            const data = await response.json();

                                            if (data.success) {
                                                popupSuccess('Password updated successfully', 'Success');

                                                // Clear form and reset
                                                document.getElementById('currentPassword').value = '';
                                                document.getElementById('newPassword').value = '';
                                                document.getElementById('confirmPassword').value = '';

                                                // Reset state
                                                otpVerified = false;
                                                verifiedCurrentPassword = '';
                                                document.getElementById('newPassword').disabled = true;
                                                document.getElementById('confirmPassword').disabled = true;
                                                document.getElementById('newPassword').classList.add('bg-gray-100', 'cursor-not-allowed');
                                                document.getElementById('confirmPassword').classList.add('bg-gray-100', 'cursor-not-allowed');
                                            } else {
                                                let errorMsg = data.message || 'Failed to update password';
                                                if (data.errors) {
                                                    errorMsg += '\n' + Object.values(data.errors).map(e => e.join(', ')).join('\n');
                                                }
                                                popupError(errorMsg, 'Error');
                                            }
                                        } catch (error) {
                                            popupError('Failed to update password. Please try again.', 'Error');
                                        }
                                    },
                                    function () {
                                        // User clicked Cancel - Do nothing
                                        popupAlert('Password change cancelled', 'Cancelled');
                                    }
                                );
                            };
                        </script>
                        <div
                            class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <div
                                class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 px-4 py-3 rounded-xl">
                                <div
                                    class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-info-circle text-indigo-500 dark:text-indigo-400"></i>
                                </div>
                                <span>Password must be at least 8 characters long</span>
                            </div>
                            <button onclick="updatePassword()"
                                class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:from-indigo-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105 hover:-translate-y-0.5">
                                <i class="fas fa-save text-lg"></i>
                                Update Password
                            </button>
                        </div>
                    </div>
                </div>

                <!-- OTP Verification Modal -->
                <div id="otpModal"
                    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4 p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Verify OTP</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            We've sent an 8-digit OTP to your email address. Please enter it below to
                            confirm password
                            change.
                        </p>
                        <div>
                            <label for="otpInput"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enter
                                OTP</label>
                            <input type="text" id="otpInput" placeholder="Enter 8-digit OTP" maxlength="8"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-center text-2xl tracking-widest">
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button onclick="closeOtpModal()"
                                class="flex-1 px-4 py-3 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-semibold rounded-lg transition-colors">
                                Cancel
                            </button>
                            <button onclick="verifyOtpAndEnableFields()"
                                class="flex-1 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
                                Verify OTP
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Two-Factor Authentication Section -->
                <div class="mt-10" style="isolation: isolate;">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700 max-w-9xl mx-auto"
                        style="position: relative; z-index: 1;">
                        <div class="px-8 py-6"
                            style="background: linear-gradient(to right, #7c3aed, #6366f1); position: relative; z-index: 2;">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                    <i class="fas fa-shield-alt text-2xl text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-white">Two-Factor Authentication</h3>
                                    <p class="text-sm text-purple-100 mt-1">Enhance your account security with an extra
                                        layer of protection</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-8">
                            @if(isset($user->two_factor_secret) && $user->two_factor_secret)
                                @php
                                    $is2FAEmail = $user->two_factor_secret === 'email_otp';
                                @endphp
                                <!-- 2FA Enabled -->
                                <div class="space-y-6">
                                    <div
                                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-8 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-200 dark:border-green-700 rounded-2xl shadow-sm">
                                        <div class="flex items-start gap-4 flex-1">
                                            <div
                                                class="w-12 h-12 @if($is2FAEmail) bg-purple-100 dark:bg-purple-800/40 @else bg-green-100 dark:bg-green-800/40 @endif rounded-xl flex items-center justify-center flex-shrink-0">
                                                @if($is2FAEmail)
                                                    <i class="fas fa-envelope text-2xl text-purple-600 dark:text-purple-400"></i>
                                                @else
                                                    <i class="fas fa-mobile-alt text-2xl text-green-600 dark:text-green-400"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-lg font-semibold text-gray-900 dark:text-white mb-1">2FA
                                                    is Enabled</p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    @if($is2FAEmail)
                                                        <span
                                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs font-semibold rounded-full">
                                                            <i class="fas fa-envelope text-xs"></i>
                                                            Email OTP Method
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 text-xs font-semibold rounded-full">
                                                            <i class="fas fa-mobile-alt text-xs"></i>
                                                            Authenticator App
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Your account is
                                                    protected with
                                                    two-factor authentication</p>
                                            </div>
                                        </div>
                                        <button onclick="showDisable2FAConfirm()"
                                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all hover:shadow-lg flex-shrink-0">
                                            <i class="fas fa-times-circle mr-2"></i>Disable 2FA
                                        </button>
                                        <script>
                                            // Keep the original disable2FA available for backward-compatibility.
                                            // The button now opens a custom confirmation modal which calls
                                            // `performDisable2FA()` when the user confirms.
                                            window.showDisable2FAConfirm = function showDisable2FAConfirm() {
                                                const modal = document.getElementById('disable2faConfirmModal');
                                                if (modal) modal.classList.remove('hidden');
                                            };

                                            window.closeDisable2FAConfirm = function closeDisable2FAConfirm() {
                                                const modal = document.getElementById('disable2faConfirmModal');
                                                if (modal) modal.classList.add('hidden');
                                            };

                                            window.performDisable2FA = async function performDisable2FA() {
                                                // Show a small loading state on the Yes button
                                                const yesBtn = document.getElementById('disable2faConfirmYes');
                                                const noBtn = document.getElementById('disable2faConfirmNo');
                                                if (yesBtn) {
                                                    yesBtn.disabled = true;
                                                    yesBtn.dataset.orig = yesBtn.textContent;
                                                    yesBtn.textContent = 'Disabling...';
                                                }
                                                try {
                                                    const response = await fetch('/settings/2fa/disable', {
                                                        method: 'POST',
                                                        credentials: 'same-origin',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                            'Accept': 'application/json'
                                                        }
                                                    });

                                                    // Check if response is ok
                                                    if (!response.ok) {
                                                        console.error('Disable 2FA response not ok:', response.status, response.statusText);
                                                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                                    }

                                                    const data = await response.json();
                                                    console.log('Disable 2FA response:', data);

                                                    if (data.success) {
                                                        closeDisable2FAConfirm();
                                                        if (typeof popupSuccess === 'function') {
                                                            popupSuccess('Two-Factor Authentication has been disabled.', 'Success');
                                                        }
                                                        setTimeout(() => window.location.reload(), 800);
                                                    } else {
                                                        closeDisable2FAConfirm();
                                                        if (typeof popupError === 'function') {
                                                            popupError(data.message || 'Failed to disable 2FA. Please try again.', 'Error');
                                                        }
                                                    }
                                                } catch (error) {
                                                    console.error('Disable 2FA error:', error);
                                                    closeDisable2FAConfirm();
                                                    if (typeof popupError === 'function') {
                                                        popupError('Failed to disable 2FA. Please try again.', 'Error');
                                                    }
                                                } finally {
                                                    if (yesBtn) {
                                                        yesBtn.disabled = false;
                                                        yesBtn.textContent = yesBtn.dataset.orig || 'Yes';
                                                    }
                                                    if (noBtn) noBtn.disabled = false;
                                                }
                                            };
                                        </script>
                                        <!-- Custom confirmation modal for disabling 2FA -->
                                        <div id="disable2faConfirmModal"
                                            class="hidden fixed inset-0 z-50 flex items-center justify-center">
                                            <div class="absolute inset-0 bg-black/50" onclick="closeDisable2FAConfirm()">
                                            </div>
                                            <div
                                                class="relative max-w-md w-full bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-lg shadow-lg border border-white/50 dark:border-gray-700/50-lg p-6 mx-4">
                                                <div class="flex justify-between items-start">
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                        Confirm Disable 2FA
                                                    </h3>
                                                    <button onclick="closeDisable2FAConfirm()"
                                                        class="text-gray-400 hover:text-gray-600">&times;</button>
                                                </div>
                                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Are you sure
                                                    you want to
                                                    disable Two-Factor Authentication? If you disable it, you will need
                                                    to set it up
                                                    again from your security settings to re-enable it.</p>
                                                <div class="mt-6 flex justify-end gap-3">
                                                    <button id="disable2faConfirmNo" onclick="closeDisable2FAConfirm()"
                                                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">No</button>
                                                    <button id="disable2faConfirmYes" onclick="performDisable2FA()"
                                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">Yes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="p-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 border border-blue-200 dark:border-blue-700 rounded-2xl">
                                        <div class="flex items-start gap-4">
                                            <div
                                                class="w-12 h-12 bg-blue-100 dark:bg-blue-800/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-info-circle text-xl text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">
                                                    @if($is2FAEmail)
                                                        You'll receive a 6-digit verification code via email each time you sign in.
                                                    @else
                                                        You'll need to enter a 6-digit code from your authenticator app each time
                                                        you sign in.
                                                    @endif
                                                </p>
                                                <button id="viewRecoveryCodesBtn"
                                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-gray-800 border-2 border-blue-200 dark:border-blue-700 rounded-xl text-primary-600 hover:text-primary-700 dark:text-primary-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-sm font-semibold transition-all hover:shadow-md">
                                                    <i class="fas fa-key text-sm"></i>
                                                    View Recovery Codes
                                                </button>
                                                <script>
                                                    document.getElementById('viewRecoveryCodesBtn').addEventListener('click', async function () {
                                                        try {
                                                            const response = await fetch('/settings/2fa/recovery-codes', {
                                                                method: 'GET',
                                                                credentials: 'same-origin',
                                                                headers: {
                                                                    'Accept': 'application/json',
                                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                                                }
                                                            });

                                                            const data = await response.json();

                                                            if (data.success && data.recovery_codes) {
                                                                window.recoveryCodes = data.recovery_codes;

                                                                const codesList = document.getElementById('recoveryCodesList');
                                                                if (codesList) {
                                                                    codesList.innerHTML = data.recovery_codes
                                                                        .map((code, index) => `
                                                                                                                                <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg font-mono text-sm">
                                                                                                                                    <span class="text-gray-400 dark:text-gray-500 text-xs">${index + 1}.</span>
                                                                                                                                    <span class="text-gray-800 dark:text-gray-200 font-semibold tracking-wider">${code}</span>
                                                                                                                                </div>
                                                                                                                            `)
                                                                        .join('');
                                                                }

                                                                const modal = document.getElementById('recoveryCodesModal');
                                                                if (modal) {
                                                                    modal.classList.remove('hidden');
                                                                }

                                                                // Auto-download
                                                                setTimeout(() => {
                                                                    if (window.downloadRecoveryCodes) {
                                                                        window.downloadRecoveryCodes();
                                                                    }
                                                                }, 500);
                                                            } else {
                                                                if (typeof popupError === 'function') {
                                                                    popupError(data.message || 'Failed to load recovery codes', 'Error');
                                                                } else {
                                                                    alert(data.message || 'Failed to load recovery codes');
                                                                }
                                                            }
                                                        } catch (error) {
                                                            console.error('Recovery codes error:', error);
                                                            if (typeof popupError === 'function') {
                                                                popupError('Failed to load recovery codes. Please try again.', 'Error');
                                                            } else {
                                                                alert('Failed to load recovery codes. Please try again.');
                                                            }
                                                        }
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                    <!-- 2FA Disabled -->
                                    <div class="space-y-6">
                                        <div x-data="{ show2FAOptions: true }">
                                            <template x-if="show2FAOptions">
                                                <div
                                                    class="p-8 bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/10 dark:to-red-900/10 border-2 border-dashed border-orange-300 dark:border-orange-600 rounded-2xl">
                                                    <div class="flex items-start gap-5 mb-8">
                                                        <div
                                                            class="w-14 h-14 bg-orange-100 dark:bg-orange-800/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                                            <i
                                                                class="fas fa-exclamation-triangle text-2xl text-orange-600 dark:text-orange-400"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <p class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                                                2FA
                                                                Not Enabled
                                                            </p>
                                                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                                                Your
                                                                account is vulnerable. Enable two-factor authentication to
                                                                add an extra layer of security</p>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                        <!-- Authenticator App Method -->
                                                        <div class="group relative p-8 bg-white dark:bg-gray-800 border-2 border-primary-200 dark:border-primary-700 rounded-2xl hover:border-primary-500 dark:hover:border-primary-400 hover:shadow-xl transition-all duration-300 cursor-pointer overflow-hidden min-h-[280px] flex flex-col"
                                                            @click="show2FAOptions = false; window.enable2FAApp()">
                                                            <div
                                                                class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-4 py-1.5 rounded-bl-xl shadow-md">
                                                                <i class="fas fa-star text-xs mr-1"></i>RECOMMENDED
                                                            </div>
                                                            <div class="text-center flex-1 flex flex-col justify-center pt-6">
                                                                <div
                                                                    class="w-20 h-20 bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/30 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                                                    <i
                                                                        class="fas fa-mobile-alt text-4xl text-primary-600 dark:text-primary-400"></i>
                                                                </div>
                                                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                                                                    Authenticator App
                                                                </h4>
                                                                <p
                                                                    class="text-sm text-gray-600 dark:text-gray-400 mb-5 px-2 leading-relaxed">
                                                                    Use
                                                                    Google Authenticator, Authy, or similar apps</p>
                                                                <div
                                                                    class="flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                                                                    <i class="fas fa-shield-check text-green-500 text-sm"></i>
                                                                    <span>Most Secure</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Email/SMS OTP Method -->
                                                        <div class="group relative p-8 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-2xl hover:border-primary-500 dark:hover:border-primary-400 hover:shadow-xl transition-all duration-300 cursor-pointer min-h-[280px] flex flex-col"
                                                            @click="show2FAOptions = false; window.enable2FAEmail()">
                                                            <div class="text-center flex-1 flex flex-col justify-center">
                                                                <div
                                                                    class="w-20 h-20 bg-gradient-to-br from-purple-100 to-blue-100 dark:from-purple-900/30 dark:to-blue-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                                                    <i
                                                                        class="fas fa-envelope text-4xl text-purple-600 dark:text-purple-400"></i>
                                                                </div>
                                                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                                                                    Email OTP
                                                                </h4>
                                                                <p
                                                                    class="text-sm text-gray-600 dark:text-gray-400 mb-5 px-2 leading-relaxed">
                                                                    Receive codes via email
                                                                </p>
                                                                <div
                                                                    class="flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                                                                    <i class="fas fa-bolt text-blue-500 text-sm"></i>
                                                                    <span>Quick Setup</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            @endif
                    </div>
                </div>

                <!-- Active Sessions Section -->
                <div class="mt-10 max-w-9xl mx-auto" style="isolation: isolate;">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700"
                        style="position: relative; z-index: 1;">
                        <div class="px-8 py-6"
                            style="background: linear-gradient(to right, #2563eb, #0891b2); position: relative; z-index: 2;">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <i class="fas fa-desktop text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-white">Active Sessions</h3>
                                        <p class="text-sm text-blue-100 mt-1">Monitor and manage devices logged into your
                                            account
                                            ({{ count($sessions) }} active)</p>
                                    </div>
                                </div>
                                @if(count($sessions) > 1)
                                    <button onclick="revokeAllOtherSessions()"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-white/90 hover:bg-white text-blue-600 font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all flex-shrink-0 border border-white">
                                        <i class="fas fa-sign-out-alt text-sm"></i>
                                        Revoke All Others
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="p-8">
                            <div id="sessionsContainer" class="space-y-4">
                                @forelse($sessions as $session)
                                    @php
                                        // Device icon
                                        $deviceLower = strtolower($session->device ?? '');
                                        if (str_contains($deviceLower, 'mobile') || str_contains($deviceLower, 'phone') || str_contains($deviceLower, 'android') || str_contains($deviceLower, 'iphone')) {
                                            $deviceIcon = 'fas fa-mobile-alt';
                                        } elseif (str_contains($deviceLower, 'tablet') || str_contains($deviceLower, 'ipad')) {
                                            $deviceIcon = 'fas fa-tablet-alt';
                                        } else {
                                            $deviceIcon = 'fas fa-desktop';
                                        }

                                        // Browser icon
                                        $browserLower = strtolower($session->browser ?? '');
                                        if (str_contains($browserLower, 'chrome')) {
                                            $browserIcon = 'fab fa-chrome';
                                        } elseif (str_contains($browserLower, 'firefox')) {
                                            $browserIcon = 'fab fa-firefox';
                                        } elseif (str_contains($browserLower, 'safari')) {
                                            $browserIcon = 'fab fa-safari';
                                        } elseif (str_contains($browserLower, 'edge')) {
                                            $browserIcon = 'fab fa-edge';
                                        } elseif (str_contains($browserLower, 'opera')) {
                                            $browserIcon = 'fab fa-opera';
                                        } else {
                                            $browserIcon = 'fas fa-globe';
                                        }

                                        // Time calculations
                                        $lastActivity = \Carbon\Carbon::parse($session->last_activity);
                                        $diffMinutes = now()->diffInMinutes($lastActivity);
                                        $diffHours = now()->diffInHours($lastActivity);
                                        $diffDays = now()->diffInDays($lastActivity);

                                        if ($diffMinutes < 1) {
                                            $lastActivityStr = 'Active now';
                                        } elseif ($diffMinutes < 60) {
                                            $lastActivityStr = $diffMinutes . ' minute' . ($diffMinutes > 1 ? 's' : '') . ' ago';
                                        } elseif ($diffHours < 24) {
                                            $lastActivityStr = $diffHours . ' hour' . ($diffHours > 1 ? 's' : '') . ' ago';
                                        } elseif ($diffDays < 30) {
                                            $lastActivityStr = $diffDays . ' day' . ($diffDays > 1 ? 's' : '') . ' ago';
                                        } else {
                                            $lastActivityStr = $lastActivity->format('M d, Y');
                                        }

                                        $loginTime = $session->login_time ? \Carbon\Carbon::parse($session->login_time)->format('M d, Y h:i A') : null;
                                    @endphp

                                    <div class="session-card group relative bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-750 border-2 {{ $session->is_current ? 'border-blue-400 dark:border-blue-600' : 'border-gray-200 dark:border-gray-700' }} rounded-2xl p-6 hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-300 hover:shadow-lg"
                                        data-session-id="{{ $session->id }}">
                                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                            <!-- Left Section: Device Info -->
                                            <div class="flex items-start gap-4 flex-1">
                                                <!-- Device Icon -->
                                                <div
                                                    class="w-14 h-14 bg-gradient-to-br {{ $session->is_current ? 'from-blue-500 to-cyan-500' : 'from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30' }} rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                                    <i
                                                        class="{{ $deviceIcon }} text-2xl {{ $session->is_current ? 'text-white' : 'text-blue-600 dark:text-blue-400' }}"></i>
                                                </div>

                                                <!-- Session Details -->
                                                <div class="flex-1 min-w-0">
                                                    <!-- Browser & Platform -->
                                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                                        <i
                                                            class="{{ $browserIcon }} text-lg text-gray-500 dark:text-gray-400"></i>
                                                        <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                                            {{ $session->browser ?? 'Unknown Browser' }}
                                                        </h4>
                                                        @if($session->is_current)
                                                            <span
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full border border-blue-200 dark:border-blue-800">
                                                                <i class="fas fa-check-circle text-xs"></i>This Device
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <!-- Platform/OS -->
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                        <i class="fas fa-microchip text-xs mr-1.5"></i>
                                                        {{ $session->platform ?? $session->device ?? 'Unknown Platform' }}
                                                    </p>

                                                    <!-- Location & IP -->
                                                    <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">
                                                        <i class="fas fa-map-marker-alt text-xs mr-1.5"></i>
                                                        {{ $session->ip_address ?? 'Unknown IP' }}
                                                        @if($session->location)
                                                            • {{ $session->location }}
                                                        @endif
                                                    </p>

                                                    <!-- Activity Time -->
                                                    <div class="flex flex-wrap items-center gap-3 mt-3">
                                                        <span
                                                            class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                                            <i class="far fa-clock text-xs"></i>
                                                            <strong>Last active:</strong> {{ $lastActivityStr }}
                                                        </span>
                                                        @if($loginTime)
                                                            <span
                                                                class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                                                <i class="fas fa-sign-in-alt text-xs"></i>
                                                                <strong>Logged in:</strong> {{ $loginTime }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right Section: Status & Actions -->
                                            <div
                                                class="flex flex-col sm:flex-row items-start sm:items-center gap-3 lg:pl-4 lg:border-l-2 lg:border-gray-200 dark:lg:border-gray-700">
                                                @if($session->is_active)
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full">
                                                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                                        Active
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400 rounded-full">
                                                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                                        Inactive
                                                    </span>
                                                @endif

                                                @if(!$session->is_current)
                                                    <button onclick="revokeSession({{ $session->id }})"
                                                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-700 dark:text-red-300 font-semibold text-sm rounded-lg transition-all border border-red-200 dark:border-red-800 hover:shadow-md">
                                                        <i class="fas fa-sign-out-alt text-xs"></i>
                                                        Revoke
                                                    </button>
                                                @else
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 italic px-2">Current
                                                        session</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div
                                        class="flex flex-col items-center justify-center p-12 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/30 dark:to-gray-800/30 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                        <div
                                            class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-desktop text-3xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                        <p class="text-base text-gray-600 dark:text-gray-400 font-medium">No active sessions
                                            found</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Your session history will
                                            appear here
                                        </p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <script>
                            // Session Management - Define functions immediately so they're available for onclick
                            window.revokeSession = async function (sessionId) {
                                if (!confirm('Are you sure you want to revoke this session? The device will be logged out.')) {
                                    return;
                                }

                                const sessionCard = document.querySelector(`[data-session-id="${sessionId}"]`);
                                if (sessionCard) {
                                    const btn = sessionCard.querySelector('button');
                                    if (btn) {
                                        btn.disabled = true;
                                        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Revoking...';
                                    }
                                }

                                try {
                                    const response = await fetch(`/settings/sessions/${sessionId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Accept': 'application/json'
                                        },
                                        credentials: 'same-origin'
                                    });

                                    const data = await response.json();

                                    if (data.success) {
                                        if (sessionCard) {
                                            sessionCard.style.transition = 'all 0.3s ease';
                                            sessionCard.style.opacity = '0';
                                            sessionCard.style.transform = 'translateX(20px)';
                                            setTimeout(() => {
                                                sessionCard.remove();
                                                window.updateSessionCount();
                                            }, 300);
                                        }
                                        if (typeof popupSuccess === 'function') {
                                            popupSuccess('Session revoked successfully!', 'Success');
                                        }
                                    } else {
                                        if (typeof popupError === 'function') {
                                            popupError(data.message || 'Failed to revoke session', 'Error');
                                        }
                                        if (sessionCard) {
                                            const btn = sessionCard.querySelector('button');
                                            if (btn) {
                                                btn.disabled = false;
                                                btn.innerHTML = '<i class="fas fa-sign-out-alt text-xs"></i> Revoke';
                                            }
                                        }
                                    }
                                } catch (error) {
                                    console.error('Failed to revoke session:', error);
                                    if (typeof popupError === 'function') {
                                        popupError('Failed to revoke session. Please try again.', 'Error');
                                    }
                                    if (sessionCard) {
                                        const btn = sessionCard.querySelector('button');
                                        if (btn) {
                                            btn.disabled = false;
                                            btn.innerHTML = '<i class="fas fa-sign-out-alt text-xs"></i> Revoke';
                                        }
                                    }
                                }
                            };

                            window.revokeAllOtherSessions = async function () {
                                if (!confirm('Are you sure you want to revoke all other sessions? This will log you out from all other devices.')) {
                                    return;
                                }

                                try {
                                    const response = await fetch('/settings/sessions/revoke-all', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Accept': 'application/json'
                                        },
                                        credentials: 'same-origin'
                                    });

                                    const data = await response.json();

                                    if (data.success) {
                                        if (typeof popupSuccess === 'function') {
                                            popupSuccess('All other sessions have been revoked!', 'Success');
                                        }
                                        setTimeout(() => window.location.reload(), 1000);
                                    } else {
                                        if (typeof popupError === 'function') {
                                            popupError(data.message || 'Failed to revoke sessions', 'Error');
                                        }
                                    }
                                } catch (error) {
                                    console.error('Failed to revoke sessions:', error);
                                    if (typeof popupError === 'function') {
                                        popupError('Failed to revoke sessions. Please try again.', 'Error');
                                    }
                                }
                            };

                            window.updateSessionCount = function () {
                                const container = document.getElementById('sessionsContainer');
                                const sessionCards = container ? container.querySelectorAll('[data-session-id]') : [];
                                const count = sessionCards.length;

                                if (count === 0) {
                                    container.innerHTML = `
                                                                                                                                                                <div class="flex flex-col items-center justify-center p-12 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/30 dark:to-gray-800/30 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                                                                                                                                                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                                                                                                                                                        <i class="fas fa-desktop text-3xl text-gray-400 dark:text-gray-500"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <p class="text-base text-gray-600 dark:text-gray-400 font-medium">No active sessions found</p>
                                                                                                                                                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Your session history will appear here</p>
                                                                                                                                                                </div>
                                                                                                                                                            `;
                                }
                            };
                        </script>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="mt-6 sm:mt-8 md:mt-10 max-w-9xl mx-auto" style="isolation: isolate;">
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 dark:from-gray-800 dark:to-gray-800 border-2 border-red-400 dark:border-red-700 rounded-xl sm:rounded-2xl shadow-xl overflow-hidden"
                        style="position: relative; z-index: 1;">
                        <div class="px-4 py-3 sm:px-6 sm:py-4 md:px-8 md:py-5"
                            style="background: linear-gradient(to right, #dc2626, #b91c1c); position: relative; z-index: 2;">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div
                                    class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 backdrop-blur-sm rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-exclamation-triangle text-lg sm:text-xl text-white"></i>
                                </div>
                                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-white">Danger Zone</h2>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6 md:p-8">
                            <div
                                class="bg-white dark:bg-gray-800 border-2 border-red-200 dark:border-red-800 rounded-xl p-4 sm:p-6 md:p-8">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 sm:gap-6">
                                    <div class="flex items-start gap-3 sm:gap-5 flex-1">
                                        <div
                                            class="w-12 h-12 sm:w-14 sm:h-14 bg-red-100 dark:bg-red-900/30 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i
                                                class="fas fa-trash-alt text-xl sm:text-2xl text-red-600 dark:text-red-400"></i>
                                        </div>
                                        <div>
                                            <p
                                                class="text-base sm:text-lg md:text-xl font-bold text-red-900 dark:text-red-200 mb-1 sm:mb-2">
                                                Delete Account
                                            </p>
                                            <p class="text-xs sm:text-sm text-red-700 dark:text-red-300 leading-relaxed">
                                                Permanently
                                                delete
                                                your account and all associated data.
                                                This action is irreversible and cannot be undone.</p>
                                            <div
                                                class="mt-2 sm:mt-3 flex items-center gap-2 text-xs text-red-600 dark:text-red-400">
                                                <i class="fas fa-shield-alt"></i>
                                                <span>All your data will be permanently removed</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="showDeleteConfirm()"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-3 px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm sm:text-base font-bold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105 flex-shrink-0">
                                        <i class="fas fa-trash-alt text-base sm:text-lg"></i>
                                        Delete Account
                                    </button>
                                    <script>
                                        // Ensure the global handler exists even if other scripts fail to load
                                        if (!window.showDeleteConfirm) {
                                            window.showDeleteConfirm = function () {
                                                const modal = document.getElementById('deleteAccountModal');
                                                if (modal) modal.classList.remove('hidden');
                                            }
                                        }
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2FA Setup Modal -->
            <div id="twoFactorModal"
                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full mx-4 p-8">
                    <div class="twofa-modal-container" style="position:relative;">
                        <button onclick="close2FAModal()" aria-label="Close"
                            style="position:absolute;top:18px;right:18px;background:white;border:none;font-size:2rem;color:#2563eb;z-index:10;cursor:pointer;border-radius:50%;box-shadow:0 2px 8px rgba(31,38,135,0.08);width:40px;height:40px;display:flex;align-items:center;justify-content:center;"
                            type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <script>
                            // Guarantee global scope and availability for 2FA modal close
                            window.close2FAModal = function close2FAModal() {
                                document.getElementById('twoFactorModal').classList.add('hidden');
                                // Optionally reset modal state here if needed
                            }
                        </script>
                        <h3 class="twofa-modal-title">Setup Two-Factor Authentication</h3>
                        <div id="twoFactorStep1" class="twofa-step">
                            <div class="twofa-qr-section">
                                <div id="qrCode" class="twofa-qr"></div>
                                <div class="twofa-secret-label">Secret Key:</div>
                                <div id="secretKey" class="twofa-secret"></div>
                            </div>
                            <button onclick="showVerificationStep()" class="twofa-btn twofa-btn-primary">Next</button>
                        </div>
                        <div id="twoFactorStep2" class="twofa-step hidden">
                            <div class="twofa-inputs-wrapper">
                                <div class="twofa-step2-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <label for="verifyCode" class="twofa-label">Enter 6-digit code from your Authenticator
                                    app:</label>
                                <p class="twofa-sublabel">Open your authenticator app and enter the code shown</p>
                                <div class="twofa-inputs">
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="twofa-input" id="code1" autocomplete="one-time-code" data-index="1" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="twofa-input" id="code2" autocomplete="one-time-code" data-index="2" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="twofa-input" id="code3" autocomplete="one-time-code" data-index="3" />
                                    <span class="twofa-input-separator">-</span>
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="twofa-input" id="code4" autocomplete="one-time-code" data-index="4" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="twofa-input" id="code5" autocomplete="one-time-code" data-index="5" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="twofa-input" id="code6" autocomplete="one-time-code" data-index="6" />
                                </div>
                                <input type="hidden" id="verifyCode" />
                            </div>
                            <script>
                                    // Auto-advance 2FA code inputs
                                    (function () {
                                        const inputs = document.querySelectorAll('#twoFactorStep2 .twofa-input');

                                        inputs.forEach((input, index) => {
                                            // Handle input - only allow numbers and auto-advance
                                            input.addEventListener('input', function (e) {
                                                // Remove any non-numeric characters
                                                this.value = this.value.replace(/[^0-9]/g, '');

                                                // If a digit was entered, move to next input
                                                if (this.value.length === 1 && index < inputs.length - 1) {
                                                    inputs[index + 1].focus();
                                                    inputs[index + 1].select();
                                                }

                                                // Auto-submit when all 6 digits are entered
                                                let allFilled = true;
                                                inputs.forEach(inp => {
                                                    if (!inp.value || inp.value.length !== 1) allFilled = false;
                                                });
                                                if (allFilled && index === inputs.length - 1) {
                                                    // Optional: auto-verify when complete
                                                    // window.verify2FA();
                                                }
                                            });

                                            // Handle backspace - move to previous input
                                            input.addEventListener('keydown', function (e) {
                                                if (e.key === 'Backspace') {
                                                    if (this.value === '' && index > 0) {
                                                        // If current input is empty, move to previous and clear it
                                                        inputs[index - 1].focus();
                                                        inputs[index - 1].value = '';
                                                        e.preventDefault();
                                                    }
                                                } else if (e.key === 'ArrowLeft' && index > 0) {
                                                    inputs[index - 1].focus();
                                                    e.preventDefault();
                                                } else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                                                    inputs[index + 1].focus();
                                                    e.preventDefault();
                                                }
                                            });

                                            // Select all content on focus for easy replacement
                                            input.addEventListener('focus', function () {
                                                this.select();
                                            });

                                            // Handle paste - distribute digits across inputs
                                            input.addEventListener('paste', function (e) {
                                                e.preventDefault();
                                                const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                                                const digits = pastedData.replace(/[^0-9]/g, '').split('');

                                                inputs.forEach((inp, i) => {
                                                    if (digits[i]) {
                                                        inp.value = digits[i];
                                                    }
                                                });

                                                // Focus the next empty input or the last one
                                                const nextEmpty = Array.from(inputs).findIndex(inp => !inp.value);
                                                if (nextEmpty !== -1) {
                                                    inputs[nextEmpty].focus();
                                                } else {
                                                    inputs[inputs.length - 1].focus();
                                                }
                                            });
                                        });
                                    })();
                            </script>
                            <div class="twofa-actions">
                                <button id="twofaVerifyBtn" onclick="verify2FA()" class="twofa-btn twofa-btn-primary">
                                    <i class="fas fa-shield-alt" id="twofaVerifyIcon"></i>
                                    <span id="twofaVerifyText">Verify & Enable</span>
                                </button>
                                <button id="twofaCancelBtn" onclick="close2FAModal()" class="twofa-btn twofa-btn-secondary">
                                    <i class="fas fa-times"></i>
                                    <span>Cancel</span>
                                </button>
                            </div>
                            <script>
                                // Guarantee global scope and correct code collection for 2FA verification
                                window.verify2FA = async function verify2FA() {
                                    // Get button elements
                                    const verifyBtn = document.getElementById('twofaVerifyBtn');
                                    const cancelBtn = document.getElementById('twofaCancelBtn');
                                    const verifyIcon = document.getElementById('twofaVerifyIcon');
                                    const verifyText = document.getElementById('twofaVerifyText');

                                    // Collect all 6 digits from the input boxes
                                    let code = '';
                                    for (let i = 1; i <= 6; i++) {
                                        const val = document.getElementById('code' + i).value;
                                        if (!val || val.length !== 1 || !/^[0-9]$/.test(val)) {
                                            popupError('Please enter all 6 digits.', 'Error');
                                            return;
                                        }
                                        code += val;
                                    }
                                    // Optionally set the hidden input for backend compatibility
                                    document.getElementById('verifyCode').value = code;

                                    // Show loading effect
                                    if (verifyBtn) {
                                        verifyBtn.disabled = true;
                                        verifyBtn.classList.add('twofa-btn-loading');
                                    }
                                    if (cancelBtn) {
                                        cancelBtn.disabled = true;
                                        cancelBtn.classList.add('twofa-btn-disabled');
                                    }
                                    if (verifyIcon) {
                                        verifyIcon.className = 'fas fa-spinner fa-spin';
                                    }
                                    if (verifyText) {
                                        verifyText.textContent = 'Verifying...';
                                    }

                                    // Send code to backend
                                    try {
                                        const response = await fetch('/settings/2fa/verify', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                            },
                                            credentials: 'same-origin',
                                            body: JSON.stringify({ code })
                                        });
                                        const result = await response.json();
                                        if (result.success) {
                                            // Show success state
                                            if (verifyIcon) {
                                                verifyIcon.className = 'fas fa-check';
                                            }
                                            if (verifyText) {
                                                verifyText.textContent = 'Success!';
                                            }
                                            popupSuccess('2FA enabled successfully! Reloading...', 'Success');
                                            // Reload entire page after short delay
                                            setTimeout(function () {
                                                window.location.reload();
                                            }, 1500);
                                        } else {
                                            // Reset button state on error
                                            if (verifyBtn) {
                                                verifyBtn.disabled = false;
                                                verifyBtn.classList.remove('twofa-btn-loading');
                                            }
                                            if (cancelBtn) {
                                                cancelBtn.disabled = false;
                                                cancelBtn.classList.remove('twofa-btn-disabled');
                                            }
                                            if (verifyIcon) {
                                                verifyIcon.className = 'fas fa-shield-alt';
                                            }
                                            if (verifyText) {
                                                verifyText.textContent = 'Verify & Enable';
                                            }
                                            popupError(result.message || 'Failed to verify code. Please try again.', 'Error');
                                        }
                                    } catch (error) {
                                        // Reset button state on error
                                        if (verifyBtn) {
                                            verifyBtn.disabled = false;
                                            verifyBtn.classList.remove('twofa-btn-loading');
                                        }
                                        if (cancelBtn) {
                                            cancelBtn.disabled = false;
                                            cancelBtn.classList.remove('twofa-btn-disabled');
                                        }
                                        if (verifyIcon) {
                                            verifyIcon.className = 'fas fa-shield-alt';
                                        }
                                        if (verifyText) {
                                            verifyText.textContent = 'Verify & Enable';
                                        }
                                        popupError('Failed to verify code. Please try again.', 'Error');
                                    }
                                }
                            </script>
                        </div>
                        <div class="twofa-modal-footer">
                            <span class="twofa-footer-text">Need help? <a href="/help/2fa" class="twofa-footer-link">Learn
                                    more about 2FA</a></span>
                        </div>
                    </div>
                    <style>
                        .twofa-modal-container {
                            background: rgba(255, 255, 255, 0.85);
                            border-radius: 1.25rem;
                            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
                            backdrop-filter: blur(8px);
                            -webkit-backdrop-filter: blur(8px);
                            border: 1px solid rgba(255, 255, 255, 0.18);
                            max-width: 600px;
                            width: 100%;
                            margin: 0 auto;
                            padding: 2rem 1.5rem 1.5rem 1.5rem;
                            display: flex;
                            flex-direction: column;
                            align-items: stretch;
                        }

                        .twofa-modal-title {
                            font-size: 1.5rem;
                            font-weight: 700;
                            color: #222;
                            margin-bottom: 1.25rem;
                            text-align: center;
                        }

                        .twofa-step {
                            display: flex;
                            flex-direction: column;
                            gap: 1.5rem;
                            margin-bottom: 1.5rem;
                        }

                        .twofa-qr-section {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            gap: 0.75rem;
                        }

                        .twofa-qr {
                            margin-bottom: 0.5rem;
                        }

                        .twofa-secret-label {
                            font-size: 0.95rem;
                            color: #555;
                        }

                        .twofa-secret {
                            font-family: monospace;
                            font-size: 1.1rem;
                            color: #333;
                            background: #f3f4f6;
                            border-radius: 0.5rem;
                            padding: 0.25rem 0.75rem;
                        }

                        .twofa-inputs-wrapper {
                            display: flex;
                            flex-direction: column;
                            gap: 0.75rem;
                        }

                        .twofa-label {
                            font-size: 1rem;
                            color: #222;
                            margin-bottom: 0.5rem;
                        }

                        .twofa-inputs {
                            display: flex;
                            justify-content: center;
                            gap: 0.5rem;
                        }

                        .twofa-input {
                            width: 2.5rem;
                            height: 2.5rem;
                            font-size: 1.5rem;
                            text-align: center;
                            border-radius: 0.75rem;
                            border: 1px solid #d1d5db;
                            background: rgba(255, 255, 255, 0.7);
                            box-shadow: 0 2px 8px rgba(31, 38, 135, 0.08);
                            outline: none;
                            transition: border-color 0.2s;
                        }

                        .twofa-input:focus {
                            border-color: #2563eb;
                            background: #e0e7ff;
                        }

                        .twofa-actions {
                            display: flex;
                            justify-content: center;
                            gap: 1rem;
                        }

                        .twofa-step2-icon {
                            width: 64px;
                            height: 64px;
                            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 1rem auto;
                            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
                        }

                        .twofa-step2-icon i {
                            font-size: 1.75rem;
                            color: #fff;
                        }

                        .twofa-sublabel {
                            font-size: 0.875rem;
                            color: #6b7280;
                            text-align: center;
                            margin-bottom: 0.5rem;
                        }

                        .twofa-input-separator {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 1.5rem;
                            font-weight: 600;
                            color: #9ca3af;
                            margin: 0 0.25rem;
                        }

                        .twofa-btn {
                            padding: 0.65rem 1.5rem;
                            border-radius: 0.75rem;
                            font-size: 1rem;
                            font-weight: 600;
                            cursor: pointer;
                            border: none;
                            transition: all 0.3s ease;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            gap: 0.5rem;
                            min-width: 140px;
                        }

                        .twofa-btn-primary {
                            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                            color: #fff;
                            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
                        }

                        .twofa-btn-primary:hover:not(:disabled) {
                            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
                            transform: translateY(-2px);
                            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
                        }

                        .twofa-btn-secondary {
                            background: #f3f4f6;
                            color: #374151;
                            border: 1px solid #e5e7eb;
                        }

                        .twofa-btn-secondary:hover:not(:disabled) {
                            background: #e5e7eb;
                            transform: translateY(-2px);
                        }

                        .twofa-btn-loading {
                            opacity: 0.9;
                            cursor: not-allowed;
                        }

                        .twofa-btn-disabled {
                            opacity: 0.5;
                            cursor: not-allowed;
                        }

                        .twofa-btn:disabled {
                            transform: none !important;
                        }

                        .twofa-modal-container {
                            background: rgba(255, 255, 255, 0.85);
                            border-radius: 1.25rem;
                            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
                            backdrop-filter: blur(8px);
                            -webkit-backdrop-filter: blur(8px);
                            border: 1px solid rgba(255, 255, 255, 0.18);
                            max-width: 600px;
                            width: 100%;
                            margin: 0 auto;
                            padding: 2.5rem 2rem 2rem 2rem;
                            display: flex;
                            flex-direction: column;
                            align-items: stretch;
                            position: relative;
                        }

                        @media (max-width: 600px) {
                            .twofa-modal-container {
                                max-width: 98vw;
                                padding: 1.25rem 0.5rem 1rem 0.5rem;
                            }

                            .twofa-input {
                                width: 2rem;
                                height: 2rem;
                                font-size: 1.2rem;
                            }
                        }

                        @media (max-width: 400px) {
                            .twofa-modal-title {
                                font-size: 1.1rem;
                            }

                            .twofa-input {
                                width: 1.5rem;
                                height: 1.5rem;
                                font-size: 1rem;
                            }
                        }
                    </style>
                </div>
            </div>

            <!-- Email OTP 2FA Setup Modal -->
            <div id="emailOtpModal"
                class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden relative">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <i class="fas fa-envelope text-2xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Email OTP Verification</h3>
                                <p class="text-purple-100 text-sm">Secure your account with email codes</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Send Code -->
                    <div id="emailOtpStep1" class="p-6 space-y-6">
                        <div class="text-center">
                            <div
                                class="w-20 h-20 bg-gradient-to-br from-purple-100 to-blue-100 dark:from-purple-900/30 dark:to-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-paper-plane text-3xl text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Verify Your Email</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                We'll send a 6-digit verification code to:<br>
                                <span
                                    class="font-semibold text-purple-600 dark:text-purple-400">{{ auth()->user()->email ?? 'your email' }}</span>
                            </p>
                        </div>

                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                <div>
                                    <p class="text-sm text-blue-800 dark:text-blue-200">
                                        Every time you log in, you'll receive a verification code via email to confirm your
                                        identity.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button onclick="sendEmailOtpVerification()" id="sendEmailOtpBtn"
                            class="w-full py-4 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Send Verification Code</span>
                        </button>
                    </div>

                    <!-- Step 2: Enter Code -->
                    <div id="emailOtpStep2" class="hidden p-6 space-y-6">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-teal-100 to-green-100 dark:from-teal-900/30 dark:to-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-shield-check text-2xl text-teal-600 dark:text-teal-400"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Enter Verification Code
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                We've sent a 6-digit code to your email. Enter it below:
                            </p>
                        </div>

                        <div class="flex justify-center gap-2">
                            <input type="text" id="emailOtp1" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all email-otp-input" />
                            <input type="text" id="emailOtp2" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all email-otp-input" />
                            <input type="text" id="emailOtp3" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all email-otp-input" />
                            <input type="text" id="emailOtp4" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all email-otp-input" />
                            <input type="text" id="emailOtp5" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all email-otp-input" />
                            <input type="text" id="emailOtp6" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all email-otp-input" />
                        </div>
                        <input type="hidden" id="emailOtpCode" />

                        <script>
                                // Email OTP input handling with auto-advance
                                (function () {
                                    const inputs = document.querySelectorAll('.email-otp-input');
                                    inputs.forEach((input, index) => {
                                        input.addEventListener('input', function (e) {
                                            this.value = this.value.replace(/[^0-9]/g, '');
                                            if (this.value.length === 1 && index < inputs.length - 1) {
                                                inputs[index + 1].focus();
                                            }
                                            // Update hidden field
                                            let code = '';
                                            inputs.forEach(inp => code += inp.value);
                                            document.getElementById('emailOtpCode').value = code;
                                        });

                                        input.addEventListener('keydown', function (e) {
                                            if (e.key === 'Backspace' && !this.value && index > 0) {
                                                inputs[index - 1].focus();
                                                inputs[index - 1].value = '';
                                                e.preventDefault();
                                            } else if (e.key === 'ArrowLeft' && index > 0) {
                                                inputs[index - 1].focus();
                                                e.preventDefault();
                                            } else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                                                inputs[index + 1].focus();
                                                e.preventDefault();
                                            }
                                        });

                                        input.addEventListener('paste', function (e) {
                                            e.preventDefault();
                                            const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                                            const digits = pastedData.replace(/[^0-9]/g, '').split('');
                                            inputs.forEach((inp, i) => {
                                                if (digits[i]) inp.value = digits[i];
                                            });
                                            let code = '';
                                            inputs.forEach(inp => code += inp.value);
                                            document.getElementById('emailOtpCode').value = code;
                                            if (digits.length >= 6) inputs[5].focus();
                                        });

                                        input.addEventListener('focus', function () { this.select(); });
                                    });
                                })();
                        </script>

                        <div class="flex gap-3">
                            <button onclick="backToEmailOtpStep1()"
                                class="flex-1 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-arrow-left text-sm"></i>
                                Back
                            </button>
                            <button onclick="verifyEmailOtp2FA()" id="verifyEmailOtpBtn"
                                class="flex-1 py-3 bg-gradient-to-r from-teal-600 to-green-600 hover:from-teal-700 hover:to-green-700 text-white font-semibold rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-shield-check"></i>
                                Verify & Enable
                            </button>
                        </div>

                        <div class="text-center pt-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Didn't receive the code?</p>
                            <button onclick="resendEmailOtp()" id="resendEmailBtn"
                                class="text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400 font-medium transition-colors">
                                <i class="fas fa-redo mr-1"></i>
                                <span id="resendEmailText">Resend Code</span>
                            </button>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <button onclick="closeEmailOtpModal()"
                        class="absolute top-4 right-4 w-8 h-8 bg-white/20 hover:bg-white/30 text-white rounded-lg flex items-center justify-center transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Email OTP Functions Script (must be inline after modal) -->
            <script>
                // Email OTP 2FA Functions - defined here to ensure they're available when modal is rendered
                window.sendEmailOtpVerification = async function () {
                    const button = document.getElementById('sendEmailOtpBtn');
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
                    }

                    try {
                        const response = await fetch('/settings/2fa/send-email-otp', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            document.getElementById('emailOtpStep1').classList.add('hidden');
                            document.getElementById('emailOtpStep2').classList.remove('hidden');
                            if (typeof popupSuccess === 'function') {
                                popupSuccess('Verification code sent to your email', 'Success');
                            }
                        } else {
                            if (typeof popupError === 'function') {
                                popupError(data.message || 'Failed to send verification code', 'Error');
                            }
                            if (button) {
                                button.disabled = false;
                                button.innerHTML = '<i class="fas fa-paper-plane"></i><span>Send Verification Code</span>';
                            }
                        }
                    } catch (error) {
                        console.error('Send email OTP error:', error);
                        if (typeof popupError === 'function') {
                            popupError('Failed to send verification code. Please try again.', 'Error');
                        }
                        if (button) {
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-paper-plane"></i><span>Send Verification Code</span>';
                        }
                    }
                };

                window.verifyEmailOtp2FA = async function () {
                    const code = document.getElementById('emailOtpCode').value;

                    if (!code || code.length !== 6) {
                        if (typeof popupError === 'function') {
                            popupError('Please enter a valid 6-digit code', 'Validation Error');
                        }
                        return;
                    }

                    try {
                        const response = await fetch('/settings/2fa/verify-email', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ code })
                        });

                        const data = await response.json();

                        if (data.success) {
                            closeEmailOtpModal();

                            // Show recovery codes
                            document.getElementById('recoveryCodesList').innerHTML = data.recovery_codes
                                .map(code => `<div class="p-2 bg-white dark:bg-gray-800 rounded">${code}</div>`)
                                .join('');
                            document.getElementById('recoveryCodesModal').classList.remove('hidden');
                            window.recoveryCodes = data.recovery_codes;

                            if (typeof popupSuccess === 'function') {
                                popupSuccess('Email OTP 2FA enabled successfully!', 'Success');
                            }

                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            if (typeof popupError === 'function') {
                                popupError(data.message || 'Invalid verification code', 'Error');
                            }
                        }
                    } catch (error) {
                        console.error('Verify email OTP error:', error);
                        if (typeof popupError === 'function') {
                            popupError('Failed to verify code. Please try again.', 'Error');
                        }
                    }
                };

                let emailOtpResendCooldown = 0;
                window.resendEmailOtp = async function () {
                    if (emailOtpResendCooldown > 0) return;

                    const button = document.getElementById('resendEmailBtn');
                    const textEl = document.getElementById('resendEmailText');

                    if (button) button.disabled = true;
                    if (textEl) textEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Sending...';

                    try {
                        const response = await fetch('/settings/2fa/send-email-otp', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            if (typeof popupSuccess === 'function') {
                                popupSuccess('New verification code sent to your email', 'Success');
                            }
                            emailOtpResendCooldown = 60;
                            updateEmailOtpResendCooldown();
                        } else {
                            if (typeof popupError === 'function') {
                                popupError(data.message || 'Failed to resend code', 'Error');
                            }
                            if (button) button.disabled = false;
                            if (textEl) textEl.innerHTML = 'Resend Code';
                        }
                    } catch (error) {
                        console.error('Resend email OTP error:', error);
                        if (typeof popupError === 'function') {
                            popupError('Failed to resend code. Please try again.', 'Error');
                        }
                        if (button) button.disabled = false;
                        if (textEl) textEl.innerHTML = 'Resend Code';
                    }
                };

                function updateEmailOtpResendCooldown() {
                    const button = document.getElementById('resendEmailBtn');
                    const textEl = document.getElementById('resendEmailText');
                    if (emailOtpResendCooldown > 0) {
                        if (textEl) textEl.textContent = `Resend in ${emailOtpResendCooldown}s`;
                        emailOtpResendCooldown--;
                        setTimeout(updateEmailOtpResendCooldown, 1000);
                    } else {
                        if (button) button.disabled = false;
                        if (textEl) textEl.innerHTML = 'Resend Code';
                    }
                }

                window.backToEmailOtpStep1 = function () {
                    document.getElementById('emailOtpStep2').classList.add('hidden');
                    document.getElementById('emailOtpStep1').classList.remove('hidden');
                    document.getElementById('emailOtpCode').value = '';
                    for (let i = 1; i <= 6; i++) {
                        const input = document.getElementById('emailOtp' + i);
                        if (input) input.value = '';
                    }
                    const sendBtn = document.getElementById('sendEmailOtpBtn');
                    if (sendBtn) {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Send Verification Code</span>';
                    }
                };

                window.closeEmailOtpModal = function () {
                    document.getElementById('emailOtpModal').classList.add('hidden');
                    document.getElementById('emailOtpCode').value = '';
                    document.getElementById('emailOtpStep1').classList.remove('hidden');
                    document.getElementById('emailOtpStep2').classList.add('hidden');
                    for (let i = 1; i <= 6; i++) {
                        const input = document.getElementById('emailOtp' + i);
                        if (input) input.value = '';
                    }
                    const sendBtn = document.getElementById('sendEmailOtpBtn');
                    if (sendBtn) {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Send Verification Code</span>';
                    }
                };

                window.enable2FAEmail = function () {
                    document.getElementById('emailOtpModal').classList.remove('hidden');
                    document.getElementById('emailOtpStep1').classList.remove('hidden');
                    document.getElementById('emailOtpStep2').classList.add('hidden');
                };
            </script>

            <!-- Recovery Codes Modal -->
            <div id="recoveryCodesModal"
                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full mx-4 overflow-hidden">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-key text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Recovery Codes</h3>
                                    <p class="text-blue-100 text-sm">Save these codes securely</p>
                                </div>
                            </div>
                            <button id="closeRecoveryModalBtn" class="text-white/80 hover:text-white transition-colors">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6">
                        <!-- Warning Banner -->
                        <div
                            class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4 mb-5">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                                <div>
                                    <p class="text-sm text-amber-800 dark:text-amber-200 font-medium">Important!</p>
                                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                                        Each code can only be used once. Store them in a safe place like a password manager.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Recovery Codes Grid -->
                        <div id="recoveryCodesList" class="grid grid-cols-2 gap-3 mb-5">
                            <!-- Codes will be populated here -->
                        </div>

                        <!-- Info Text -->
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mb-5">
                            <i class="fas fa-info-circle mr-1"></i>
                            Use these codes to access your account if you lose your authenticator device.
                        </p>

                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button id="downloadRecoveryCodesBtn"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors">
                                <i class="fas fa-download"></i>
                                Download Codes
                            </button>
                            <button id="copyRecoveryCodesBtn"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl transition-colors">
                                <i class="fas fa-copy"></i>
                                Copy All
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                // Recovery Codes Modal Handlers
                document.getElementById('closeRecoveryModalBtn').addEventListener('click', function () {
                    document.getElementById('recoveryCodesModal').classList.add('hidden');
                });

                document.getElementById('downloadRecoveryCodesBtn').addEventListener('click', function () {
                    if (!window.recoveryCodes || window.recoveryCodes.length === 0) return;

                    const date = new Date().toISOString().split('T')[0];
                    const content = `TrackFlow Recovery Codes
                                                                                                                                    ================================
                                                                                                                                    Generated: ${date}

                                                                                                                                    IMPORTANT: Store these codes in a safe place!
                                                                                                                                    Each code can only be used once to access your account 
                                                                                                                                    if you lose your authenticator device.

                                                                                                                                    Your Recovery Codes:
                                                                                                                                    --------------------
                                                                                                                                    ${window.recoveryCodes.map((code, i) => `${i + 1}. ${code}`).join('\n')}

                                                                                                                                    ================================
                                                                                                                                    Keep these codes secure and private.
                                                                                                                                    Do not share them with anyone.`;

                    const blob = new Blob([content], { type: 'text/plain' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `trackflow-recovery-codes-${date}.txt`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                });

                document.getElementById('copyRecoveryCodesBtn').addEventListener('click', function () {
                    if (!window.recoveryCodes || window.recoveryCodes.length === 0) return;

                    const codesText = window.recoveryCodes.join('\n');

                    navigator.clipboard.writeText(codesText).then(() => {
                        if (typeof popupSuccess === 'function') {
                            popupSuccess('Recovery codes copied to clipboard!', 'Copied');
                        } else {
                            alert('Recovery codes copied to clipboard!');
                        }
                    }).catch(err => {
                        console.error('Failed to copy:', err);
                        if (typeof popupError === 'function') {
                            popupError('Failed to copy codes. Please try manually.', 'Error');
                        } else {
                            alert('Failed to copy codes. Please try manually.');
                        }
                    });
                });

                // Also close modal when clicking outside
                document.getElementById('recoveryCodesModal').addEventListener('click', function (e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                    }
                });
            </script>

        </div>
        <!-- End Security Tab Content -->

        <!-- Notification Functions Script - Must be before toggle elements -->
        <script>
            // Toggle Notification with loading state and bottom-right toast
            window.toggleNotification = async function (key, value, inputElement) {
                const loader = document.getElementById(`loader-${key}`);

                // Show loading spinner
                if (loader) {
                    loader.classList.remove('hidden');
                }

                // Disable the toggle during save
                if (inputElement) {
                    inputElement.disabled = true;
                }

                try {
                    const response = await fetch('/settings/notifications', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ [key]: value ? 1 : 0 })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Show bottom-right toast notification
                        window.showNotificationToast(value ? 'Notification enabled' : 'Notification disabled', 'success');
                    } else {
                        // Revert the toggle state on error
                        if (inputElement) {
                            inputElement.checked = !value;
                        }
                        window.showNotificationToast(data.message || 'Failed to update', 'error');
                    }
                } catch (error) {
                    // Revert the toggle state on error
                    if (inputElement) {
                        inputElement.checked = !value;
                    }
                    window.showNotificationToast('Failed to update notification', 'error');
                    console.error('Notification update error:', error);
                } finally {
                    // Hide loading spinner
                    if (loader) {
                        loader.classList.add('hidden');
                    }

                    // Re-enable the toggle
                    if (inputElement) {
                        inputElement.disabled = false;
                    }
                }
            };

            // Bottom-right toast notification for notification settings
            window.showNotificationToast = function (message, type = 'success') {
                // Create toast container if it doesn't exist
                let toastContainer = document.getElementById('notification-toast-container');
                if (!toastContainer) {
                    toastContainer = document.createElement('div');
                    toastContainer.id = 'notification-toast-container';
                    toastContainer.className = 'fixed bottom-4 right-4 z-[9999] flex flex-col gap-2';
                    toastContainer.style.cssText = 'pointer-events: none;';
                    document.body.appendChild(toastContainer);
                }

                // Create toast element
                const toast = document.createElement('div');
                toast.style.cssText = 'pointer-events: auto; opacity: 0; transform: translateX(100%); transition: all 0.3s ease-out;';
                toast.className = 'flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl ' +
                    (type === 'success'
                        ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white'
                        : 'bg-gradient-to-r from-red-500 to-rose-600 text-white');

                const icon = type === 'success'
                    ? '<i class="fas fa-check-circle text-lg"></i>'
                    : '<i class="fas fa-exclamation-circle text-lg"></i>';

                toast.innerHTML = icon + '<span class="font-medium text-sm">' + message + '</span>';

                toastContainer.appendChild(toast);

                // Animate in after a tiny delay
                setTimeout(function () {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateX(0)';
                }, 10);

                // Auto dismiss after 2.5 seconds
                setTimeout(function () {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(function () {
                        if (toast.parentNode) {
                            toast.remove();
                        }
                        // Remove container if empty
                        if (toastContainer && toastContainer.children.length === 0) {
                            toastContainer.remove();
                        }
                    }, 300);
                }, 2500);
            };

            // Update Large Transaction Threshold
            window.updateLargeTransactionThreshold = async function () {
                const thresholdEl = document.getElementById('notifThreshold');
                if (!thresholdEl) return;
                const threshold = thresholdEl.value;
                const saveBtn = thresholdEl.closest('.flex').querySelector('button');

                if (!threshold || isNaN(threshold) || parseFloat(threshold) < 0) {
                    window.showNotificationToast('Please enter a valid threshold amount', 'error');
                    return;
                }

                // Show loading state on button
                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                }

                try {
                    const response = await fetch('/settings/notifications', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ large_transaction_threshold: parseFloat(threshold) })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        window.showNotificationToast('Threshold updated successfully', 'success');
                    } else {
                        window.showNotificationToast(data.message || 'Failed to update threshold', 'error');
                    }
                } catch (error) {
                    window.showNotificationToast('Failed to update threshold', 'error');
                    console.error('Threshold update error:', error);
                } finally {
                    // Restore button state
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="fas fa-check"></i> Save';
                    }
                }
            };
        </script>

        <!-- Notifications Tab Content -->
        <div x-show="activeTab === 'notifications'" x-cloak>
            <div
                class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 card-shadow p-6">
                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-bell text-2xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Notification Settings</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage your notification preferences
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 max-w-9xl mx-auto">
                    <!-- Email Notifications -->
                    <div
                        class="bg-gradient-to-br from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 border-2 border-green-200 dark:border-green-700 rounded-2xl shadow-lg p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-envelope text-xl text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Email Notifications</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Receive updates via email</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-receipt text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Transaction Alerts</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Get notified about new
                                            transactions</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div id="loader-transaction_alerts" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="notifTransactions" {{ $transactionAlerts ? 'checked' : '' }} onchange="toggleNotification('transaction_alerts', this.checked, this)"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-chart-pie text-orange-600 dark:text-orange-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Budget Alerts</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Notify when approaching budget
                                            limits</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div id="loader-budget_alerts" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-orange-600" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="notifBudgets" {{ $budgetAlerts ? 'checked' : '' }}
                                            onchange="toggleNotification('budget_alerts', this.checked, this)"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 dark:peer-focus:ring-orange-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-orange-500">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-bell text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Push Notifications</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Enable browser push
                                            notifications</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div id="loader-push_notifications" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="notifPush" {{ $pushNotif ? 'checked' : '' }}
                                            onchange="toggleNotification('push_notifications', this.checked, this)"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity & Updates -->
                    <div
                        class="bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/20 border-2 border-indigo-200 dark:border-indigo-700 rounded-2xl shadow-lg p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-chart-line text-xl text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Activity & Updates</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Stay informed about your financial
                                    activities</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-calendar-week text-indigo-600 dark:text-indigo-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Weekly Summary</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Receive a weekly spending
                                            summary every Sunday</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div id="loader-weekly_summary" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="notifWeeklySummary" {{ $weeklySummary ? 'checked' : '' }}
                                            onchange="toggleNotification('weekly_summary', this.checked, this)"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-bullseye text-yellow-600 dark:text-yellow-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Goal Progress</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Updates on your savings goals
                                            progress</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div id="loader-goal_progress" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="notifGoalProgress" {{ $goalProgress ? 'checked' : '' }}
                                            onchange="toggleNotification('goal_progress', this.checked, this)"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 dark:peer-focus:ring-yellow-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-yellow-500">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-users text-pink-600 dark:text-pink-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Group Expense Updates</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Notifications for group
                                            activities and settlements</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div id="loader-group_expense" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-pink-600" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="notifGroupExpense" {{ $groupExpense ? 'checked' : '' }}
                                            onchange="toggleNotification('group_expense', this.checked, this)"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 dark:peer-focus:ring-pink-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-pink-500">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Alerts -->
                    <div
                        class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-2xl shadow-lg p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-coins text-xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Large Transaction Alerts</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Get notified for high-value transactions
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-exclamation-triangle text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Large Transaction Alerts</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Alert for transactions above
                                            threshold</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div id="loader-large_transaction_alerts" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="notifLargeTransactions" {{ $largeTransAlerts ? 'checked' : '' }}
                                            onchange="toggleNotification('large_transaction_alerts', this.checked, this)"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="p-4 bg-white dark:bg-gray-800 rounded-xl">
                                <label for="notifThreshold"
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    <i class="fas fa-sliders-h text-blue-500 mr-2"></i>Transaction Threshold Amount
                                    ({{ $userCurrency }})
                                </label>
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-1">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-semibold">{{ $userCurrency }}</span>
                                        <input type="number" id="notifThreshold"
                                            value="{{ number_format($largeTransThreshold, 2, '.', '') }}" min="0" step="100"
                                            class="w-full pl-14 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold">
                                    </div>
                                    <button onclick="updateLargeTransactionThreshold()"
                                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors duration-200 flex items-center gap-2">
                                        <i class="fas fa-check"></i>
                                        Save
                                    </button>
                                </div>
                                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i>
                                    Transactions exceeding this amount will trigger an alert notification
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Security Alerts -->
                    <div
                        class="bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border-2 border-red-200 dark:border-red-700 rounded-2xl shadow-lg p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-shield-alt text-xl text-red-600 dark:text-red-400"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Security Alerts</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Stay informed about account security</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-sign-in-alt text-red-600 dark:text-red-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Login Alerts</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Notify when someone logs into
                                            your account</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div id="loader-login_alerts" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="notifLoginAlerts" {{ $loginAlerts ? 'checked' : '' }}
                                            onchange="toggleNotification('login_alerts', this.checked, this)"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 dark:peer-focus:ring-red-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-red-600">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-key text-amber-600 dark:text-amber-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Password Changes</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Notify when your password is
                                            changed</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="notifPasswordChange" checked disabled class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 dark:peer-focus:ring-amber-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-amber-500 cursor-not-allowed opacity-75">
                                    </div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-mobile-alt text-emerald-600 dark:text-emerald-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">New Device Login</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Alert when logging in from a new
                                            device</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div id="loader-new_device_alerts" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-emerald-600"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="notifNewDevice" {{ $newDeviceAlerts ? 'checked' : '' }}
                                            onchange="toggleNotification('new_device_alerts', this.checked, this)"
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 dark:peer-focus:ring-emerald-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-500">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Notifications Tab Content -->

@endsection


        <!-- Modals Section -->
        @section('modals')
            <!-- Crop Modal for Profile Picture -->
            <div id="cropModalSettings" class="fixed inset-0 bg-black bg-opacity-75 z-50 items-center justify-center p-4"
                style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Crop Profile Picture</h3>
                            <button onclick="closeCropModalSettings()"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="max-h-[50vh] overflow-hidden bg-gray-100 dark:bg-gray-900 rounded-lg">
                            <img id="cropImageSettings" class="max-w-full" style="display:block;">
                        </div>

                        <div class="mt-4 flex items-center gap-4">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Zoom:</label>
                            <input type="range" id="zoomRangeSettings" min="-1" max="1" step="0.01" value="0" class="flex-1"
                                oninput="handleZoomSettings(this.value)">
                            <button onclick="resetCropperSettings()"
                                class="px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-lg transition-colors">Reset</button>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                        <button onclick="closeCropModalSettings()"
                            class="px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button onclick="saveCroppedImageSettings()"
                            class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                            Done
                        </button>
                    </div>
                </div>
            </div>

            <!-- Camera Modal -->
            <div id="cameraModalSettings"
                class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Take Photo</h3>
                            <button onclick="closeCameraModalSettings()"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <video id="cameraVideoSettings" autoplay class="w-full max-h-[50vh] bg-gray-900 rounded-lg"></video>
                        <canvas id="cameraCanvasSettings" class="hidden"></canvas>
                    </div>

                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-center gap-3">
                        <button onclick="capturePhotoSettings()"
                            class="px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-camera"></i>
                            Capture
                        </button>
                        <button onclick="closeCameraModalSettings()"
                            class="px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Image Zoom Modal -->
            <div id="zoomModalSettings"
                class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4"
                onclick="closeZoomModalSettings()">
                <div class="relative max-w-6xl w-full">
                    <button onclick="closeZoomModalSettings()"
                        class="absolute top-4 right-4 bg-white/20 hover:bg-white/30 text-white rounded-full p-2 backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                    <img id="zoomImageSettings" class="w-full h-auto rounded-lg shadow-2xl"
                        onclick="event.stopPropagation()">
                </div>
            </div>
        @endsection

        @push('scripts')
            <script>
                // Test Cropper.js loading
                document.addEventListener('DOMContentLoaded', function () {
                    console.log('Cropper.js loaded:', typeof Cropper !== 'undefined');
                    console.log('Modal element exists:', !!document.getElementById('cropModalSettings'));
                    console.log('Image element exists:', !!document.getElementById('cropImageSettings'));
                });

                // --- Fix: Guarantee global scope and availability for OTP verification ---
                // Provide safe global defaults to avoid ReferenceErrors from inline handlers
                window.verifiedCurrentPassword = window.verifiedCurrentPassword || '';
                window.otpVerified = window.otpVerified || false;
                window.verifyOtpAndEnableFields = async function verifyOtpAndEnableFields() {
                    const otp = document.getElementById('otpInputField').value;
                    const otpTick = document.getElementById('otpSuccessTick');
                    const otpErrorMsg = document.getElementById('otpErrorMsg');
                    // Hide tick and error by default
                    otpTick.classList.add('hidden');
                    otpErrorMsg.classList.add('hidden');
                    otpErrorMsg.textContent = '';

                    if (!otp || otp.length !== 8) {
                        otpErrorMsg.textContent = 'Please enter a valid 8-digit OTP';
                        otpErrorMsg.classList.remove('hidden');
                        return;
                    }

                    try {
                        const response = await fetch('/settings/verify-otp', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ otp: otp })
                        });

                        const data = await response.json();

                        if (data.success) {
                            otpVerified = true;
                            // Show green tick
                            otpTick.classList.remove('hidden');
                            // Enable new password fields
                            const newPasswordField = document.getElementById('newPassword');
                            const confirmPasswordField = document.getElementById('confirmPassword');
                            newPasswordField.disabled = false;
                            confirmPasswordField.disabled = false;
                            newPasswordField.classList.remove('bg-gray-100', 'cursor-not-allowed');
                            confirmPasswordField.classList.remove('bg-gray-100', 'cursor-not-allowed');
                            newPasswordField.focus();
                        } else {
                            otpVerified = false;
                            // Show custom error message for 3 seconds
                            otpErrorMsg.textContent = 'OTP not Match Put The Currect OTP !';
                            otpErrorMsg.classList.remove('hidden');
                            setTimeout(() => {
                                otpErrorMsg.classList.add('hidden');
                            }, 3000);
                        }
                    } catch (error) {
                        otpVerified = false;
                        otpErrorMsg.textContent = 'Failed to verify OTP. Please try again.';
                        otpErrorMsg.classList.remove('hidden');
                        setTimeout(() => {
                            otpErrorMsg.classList.add('hidden');
                        }, 3000);
                    }
                }


                // Show OTP in Notification Panel with 5 second overview
                window.showOtpNotification = function showOtpNotification(otp) {
                    // Create notification element
                    const notificationHtml = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div id="otpNotification" class="bg-blue-500 text-white px-4 py-3 rounded-lg shadow-lg mb-3 animate-slide-in">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="flex items-center justify-between">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="flex items-center gap-3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <i class="fas fa-key text-2xl"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <p class="font-semibold">Password Change OTP</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <p class="text-sm">Your OTP: <span class="font-mono font-bold text-lg tracking-wider">${otp}</span></p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <p class="text-xs opacity-90">Valid for 10 minutes</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <button onclick="closeOtpNotification()" class="text-white hover:text-gray-200">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <i class="fas fa-times"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    `;

                    // Add to notification panel (you may need to         adjust the selector based on your notification panel structure)
                    const notificationContainer = document.querySelector('.notification-container') || document.body;
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = notificationHtml;
                    notificationContainer.prepend(tempDiv.firstElementChild);

                    // Show alert with OTP
                    popupAlert(`Your OTP is: ${otp}\n\nThis will be visible for 5 seconds.`, 'OTP Received');

                    // Auto-remove after 5 seconds
                    setTimeout(() => {
                        const otpNotif = document.getElementById('otpNotification');
                        if (otpNotif) {
                            otpNotif.style.opacity = '0';
                            otpNotif.style.transform = 'translateX(100%)';
                            setTimeout(() => otpNotif.remove(), 300);
                        }
                    }, 5000);
                }

                window.closeOtpNotification = function closeOtpNotification() {
                    const otpNotif = document.getElementById('otpNotification');
                    if (otpNotif) {
                        otpNotif.style.opacity = '0';
                        otpNotif.style.transform = 'translateX(100%)';
                        setTimeout(() => otpNotif.remove(), 300);
                    }
                }

                // Step 2: Verify OTP and Enable New Password Fields
                // --- Ensure this is defined at the very top of the script block, outside any DOMContentLoaded or function scope ---
                window.verifyOtpAndEnableFields = async function verifyOtpAndEnableFields() {
                    const otp = document.getElementById('otpInputField').value;
                    const otpTick = document.getElementById('otpSuccessTick');
                    const otpErrorMsg = document.getElementById('otpErrorMsg');
                    // Hide tick and error by default
                    otpTick.classList.add('hidden');
                    otpErrorMsg.classList.add('hidden');
                    otpErrorMsg.textContent = '';

                    if (!otp || otp.length !== 8) {
                        otpErrorMsg.textContent = 'Please enter a valid 8-digit OTP';
                        otpErrorMsg.classList.remove('hidden');
                        return;
                    }

                    try {
                        const response = await fetch('/settings/verify-otp', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ otp: otp })
                        });

                        const data = await response.json();

                        if (data.success) {
                            otpVerified = true;
                            // Show green tick
                            otpTick.classList.remove('hidden');
                            // Enable new password fields
                            const newPasswordField = document.getElementById('newPassword');
                            const confirmPasswordField = document.getElementById('confirmPassword');
                            newPasswordField.disabled = false;
                            confirmPasswordField.disabled = false;
                            newPasswordField.classList.remove('bg-gray-100', 'cursor-not-allowed');
                            confirmPasswordField.classList.remove('bg-gray-100', 'cursor-not-allowed');
                            newPasswordField.focus();
                        } else {
                            otpVerified = false;
                            // Show custom error message for 3 seconds
                            otpErrorMsg.textContent = 'OTP not Match Put The Currect OTP !';
                            otpErrorMsg.classList.remove('hidden');
                            setTimeout(() => {
                                otpErrorMsg.classList.add('hidden');
                            }, 3000);
                        }
                    } catch (error) {
                        otpVerified = false;
                        otpErrorMsg.textContent = 'Failed to verify OTP. Please try again.';
                        otpErrorMsg.classList.remove('hidden');
                        setTimeout(() => {
                            otpErrorMsg.classList.add('hidden');
                        }, 3000);
                    }
                }

                // Step 3: Update Password with Confirmation Popup
                window.updatePassword = async function updatePassword() {
                    // Prefer the actual input value; fall back to any previously stored verified password
                    const currentInput = document.getElementById('currentPassword');
                    const currentPassword = (currentInput && currentInput.value) || window.verifiedCurrentPassword || '';
                    const newPassword = document.getElementById('newPassword').value;
                    const confirmPassword = document.getElementById('confirmPassword').value;

                    if (!otpVerified) {
                        popupError('Please verify OTP first', 'Error');
                        return;
                    }

                    if (document.getElementById('newPassword').disabled || document.getElementById('confirmPassword').disabled) {
                        popupError('Please verify OTP and enter new password.', 'Error');
                        return;
                    }
                    if (!newPassword || !confirmPassword) {
                        popupError('Please fill in new password fields', 'Error');
                        return;
                    }
                    if (newPassword !== confirmPassword) {
                        popupError('New passwords do not match', 'Error');
                        return;
                    }
                    if (newPassword.length < 8) {
                        popupError('Password must be at least 8 characters', 'Error');
                        return;
                    }

                    // Show confirmation popup
                    popupConfirm(
                        'Are you sure you want to change your password?',
                        'Confirm Password Change',
                        async function () {
                            // User clicked Confirm - Update password
                            try {
                                const response = await fetch('/settings/update-password', {
                                    method: 'POST',
                                    credentials: 'same-origin',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        current_password: currentPassword,
                                        new_password: newPassword,
                                        new_password_confirmation: confirmPassword
                                    })
                                });

                                const data = await response.json();

                                if (data.success) {
                                    popupSuccess('Password updated successfully', 'Success');

                                    // Clear form and reset
                                    document.getElementById('currentPassword').value = '';
                                    document.getElementById('newPassword').value = '';
                                    document.getElementById('confirmPassword').value = '';

                                    // Reset state
                                    otpVerified = false;
                                    verifiedCurrentPassword = '';
                                    document.getElementById('newPassword').disabled = true;
                                    document.getElementById('confirmPassword').disabled = true;
                                    document.getElementById('newPassword').classList.add('bg-gray-100', 'cursor-not-allowed');
                                    document.getElementById('confirmPassword').classList.add('bg-gray-100', 'cursor-not-allowed');
                                } else {
                                    let errorMsg = data.message || 'Failed to update password';
                                    if (data.errors) {
                                        errorMsg += '\n' + Object.values(data.errors).map(e => e.join(', ')).join('\n');
                                    }
                                    popupError(errorMsg, 'Error');
                                }
                            } catch (error) {
                                popupError('Failed to update password. Please try again.', 'Error');
                            }
                        },
                        function () {
                            // User clicked Cancel - Do nothing
                            popupAlert('Password change cancelled', 'Cancelled');
                        }
                    );
                }

                // Language Change
                window.changeLanguage = async function changeLanguage() {
                    const language = document.getElementById('languageSelect').value;

                    try {
                        const response = await fetch('/settings/preferences', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ language })
                        });

                        const data = await response.json();

                        if (data.success) {
                            popupSuccess('Language updated successfully', 'Success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            popupError(data.message, 'Error');
                        }
                    } catch (error) {
                        popupError('Failed to update language', 'Error');
                        console.error('Language update error:', error);
                    }
                }

                // Two-Factor Authentication Functions
                // Clean, single implementation for enabling 2FA with Authenticator app
                window.enable2FAApp = async function enable2FAApp() {
                    try {
                        const response = await fetch('/settings/2fa/enable', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            document.getElementById('qrCode').innerHTML = data.qr_code;
                            document.getElementById('secretKey').textContent = data.secret;
                            document.getElementById('twoFactorModal').classList.remove('hidden');
                            document.getElementById('twoFactorStep1').classList.remove('hidden');
                            document.getElementById('twoFactorStep2').classList.add('hidden');
                        } else {
                            popupError(data.message || 'Failed to generate 2FA setup', 'Error');
                        }
                    } catch (error) {
                        console.error('2FA enable error:', error);
                        popupError('Failed to enable 2FA. Please try again.', 'Error');
                    }
                }

                // Helper to switch to verification step inside the 2FA modal
                function showVerificationStep() {
                    const step1 = document.getElementById('twoFactorStep1');
                    const step2 = document.getElementById('twoFactorStep2');
                    if (step1) step1.classList.add('hidden');
                    if (step2) {
                        step2.classList.remove('hidden');
                        const verifyEl = document.getElementById('verifyCode');
                        if (verifyEl) verifyEl.focus();
                    }
                }

                function closeVerificationStep() {
                    const step1 = document.getElementById('twoFactorStep1');
                    const step2 = document.getElementById('twoFactorStep2');
                    if (step1) step1.classList.remove('hidden');
                    if (step2) step2.classList.add('hidden');
                    const verifyEl = document.getElementById('verifyCode');
                    if (verifyEl) verifyEl.value = '';
                }

                // Clean disable2FA implementation
                async function disable2FA() {
                    if (!confirm('Are you sure you want to disable two-factor authentication? This will make your account less secure.')) {
                        return;
                    }

                    try {
                        const response = await fetch('/settings/2fa/disable', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            popupSuccess('Two-factor authentication disabled successfully', 'Success');
                            window.showTransientNotification('Two-factor authentication disabled successfully', 'Success', 5000);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            popupError(data.message || 'Failed to disable 2FA', 'Error');
                        }
                    } catch (error) {
                        console.error('2FA disable error:', error);
                        popupError('Failed to disable 2FA. Please try again.', 'Error');
                    }
                }

                window.viewRecoveryCodes = async function viewRecoveryCodes() {
                    try {
                        console.log('Fetching recovery codes...');
                        const response = await fetch('/settings/2fa/recovery-codes', {
                            method: 'GET',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        console.log('Response status:', response.status);
                        const data = await response.json();
                        console.log('Response data:', data);

                        if (data.success && data.recovery_codes) {
                            // Store codes globally
                            window.recoveryCodes = data.recovery_codes;

                            // Populate the codes list in the modal
                            const codesList = document.getElementById('recoveryCodesList');
                            if (codesList) {
                                codesList.innerHTML = data.recovery_codes
                                    .map((code, index) => `
                                                                                                                    <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg font-mono text-sm">
                                                                                                                        <span class="text-gray-400 dark:text-gray-500 text-xs">${index + 1}.</span>
                                                                                                                        <span class="text-gray-800 dark:text-gray-200 font-semibold tracking-wider">${code}</span>
                                                                                                                    </div>
                                                                                                                `)
                                    .join('');
                            }

                            // Show the modal
                            const modal = document.getElementById('recoveryCodesModal');
                            if (modal) {
                                modal.classList.remove('hidden');
                                console.log('Modal shown');
                            } else {
                                console.error('Modal element not found');
                            }

                            // Auto-download the codes
                            setTimeout(() => downloadRecoveryCodes(), 500);
                        } else {
                            popupError(data.message || 'Failed to load recovery codes', 'Error');
                        }
                    } catch (error) {
                        console.error('Recovery codes error:', error);
                        popupError('Failed to load recovery codes. Please try again.', 'Error');
                    }
                }

                window.close2FAModal = function close2FAModal() {
                    document.getElementById('twoFactorModal').classList.add('hidden');
                    document.getElementById('verifyCode').value = '';
                    // Clear all 6 digit inputs
                    for (let i = 1; i <= 6; i++) {
                        const input = document.getElementById('code' + i);
                        if (input) input.value = '';
                    }
                    document.getElementById('twoFactorStep1').classList.remove('hidden');
                    document.getElementById('twoFactorStep2').classList.add('hidden');
                }

                // Email OTP 2FA Functions
                window.enable2FAEmail = function enable2FAEmail() {
                    document.getElementById('emailOtpModal').classList.remove('hidden');
                    document.getElementById('emailOtpStep1').classList.remove('hidden');
                    document.getElementById('emailOtpStep2').classList.add('hidden');
                }

                window.sendEmailOtpVerification = async function sendEmailOtpVerification() {
                    const button = document.getElementById('sendEmailOtpBtn') || event?.target;
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
                    }

                    try {
                        const response = await fetch('/settings/2fa/send-email-otp', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            document.getElementById('emailOtpStep1').classList.add('hidden');
                            document.getElementById('emailOtpStep2').classList.remove('hidden');
                            // Show toast notification in bottom-right corner
                            if (typeof toastOtp === 'function') {
                                toastOtp('{{ auth()->user()->email ?? "your email" }}');
                            }
                            popupSuccess('Verification code sent to your email', 'Success');
                        } else {
                            popupError(data.message || 'Failed to send verification code', 'Error');
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Verification Code';
                        }
                    } catch (error) {
                        console.error('Send email OTP error:', error);
                        popupError('Failed to send verification code. Please try again.', 'Error');
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Verification Code';
                    }
                }

                window.verifyEmailOtp2FA = async function verifyEmailOtp2FA() {
                    const code = document.getElementById('emailOtpCode').value;

                    if (!code || code.length !== 6) {
                        popupError('Please enter a valid 6-digit code', 'Validation Error');
                        return;
                    }

                    try {
                        const response = await fetch('/settings/2fa/verify-email', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ code })
                        });

                        const data = await response.json();

                        if (data.success) {
                            closeEmailOtpModal();

                            // Show recovery codes
                            document.getElementById('recoveryCodesList').innerHTML = data.recovery_codes
                                .map(code => `<div class="p-2 bg-white dark:bg-gray-800 rounded">${code}</div>`)
                                .join('');
                            document.getElementById('recoveryCodesModal').classList.remove('hidden');
                            window.recoveryCodes = data.recovery_codes;

                            popupSuccess('Email OTP 2FA enabled successfully!', 'Success');

                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            popupError(data.message || 'Invalid verification code', 'Error');
                        }
                    } catch (error) {
                        console.error('Verify email OTP error:', error);
                        popupError('Failed to verify code. Please try again.', 'Error');
                    }
                }

                // Reuse emailOtpResendCooldown from earlier declaration
                window.resendEmailOtp = async function resendEmailOtp() {
                    if (typeof emailOtpResendCooldown !== 'undefined' && emailOtpResendCooldown > 0) return;

                    const button = document.getElementById('resendEmailBtn') || event?.target;
                    const textEl = document.getElementById('resendEmailText');

                    if (button) button.disabled = true;
                    if (textEl) textEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Sending...';

                    try {
                        const response = await fetch('/settings/2fa/send-email-otp', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            popupSuccess('New verification code sent to your email', 'Success');
                            // Start cooldown
                            emailOtpResendCooldown = 60;
                            updateEmailOtpResendCooldown();
                        } else {
                            popupError(data.message || 'Failed to resend code', 'Error');
                            if (button) button.disabled = false;
                            if (textEl) textEl.innerHTML = 'Resend Code';
                        }
                    } catch (error) {
                        console.error('Resend email OTP error:', error);
                        popupError('Failed to resend code. Please try again.', 'Error');
                        if (button) button.disabled = false;
                        if (textEl) textEl.innerHTML = 'Resend Code';
                    }
                }

                window.updateEmailOtpResendCooldown = function updateEmailOtpResendCooldown() {
                    const button = document.getElementById('resendEmailBtn');
                    const textEl = document.getElementById('resendEmailText');
                    if (emailOtpResendCooldown > 0) {
                        if (textEl) textEl.textContent = `Resend in ${emailOtpResendCooldown}s`;
                        emailOtpResendCooldown--;
                        setTimeout(updateEmailOtpResendCooldown, 1000);
                    } else {
                        if (button) button.disabled = false;
                        if (textEl) textEl.innerHTML = 'Resend Code';
                    }
                }

                window.backToEmailOtpStep1 = function backToEmailOtpStep1() {
                    document.getElementById('emailOtpStep2').classList.add('hidden');
                    document.getElementById('emailOtpStep1').classList.remove('hidden');
                    document.getElementById('emailOtpCode').value = '';
                    // Clear individual OTP inputs
                    for (let i = 1; i <= 6; i++) {
                        const input = document.getElementById('emailOtp' + i);
                        if (input) input.value = '';
                    }
                    // Reset the send button
                    const sendBtn = document.getElementById('sendEmailOtpBtn');
                    if (sendBtn) {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Send Verification Code</span>';
                    }
                }

                window.closeEmailOtpModal = function closeEmailOtpModal() {
                    document.getElementById('emailOtpModal').classList.add('hidden');
                    document.getElementById('emailOtpCode').value = '';
                    document.getElementById('emailOtpStep1').classList.remove('hidden');
                    document.getElementById('emailOtpStep2').classList.add('hidden');
                    // Clear individual OTP inputs
                    for (let i = 1; i <= 6; i++) {
                        const input = document.getElementById('emailOtp' + i);
                        if (input) input.value = '';
                    }
                    // Reset the send button
                    const sendBtn = document.getElementById('sendEmailOtpBtn');
                    if (sendBtn) {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Send Verification Code</span>';
                    }
                }

                window.closeRecoveryCodesModal = function closeRecoveryCodesModal() {
                    document.getElementById('recoveryCodesModal').classList.add('hidden');
                }

                window.downloadRecoveryCodes = function downloadRecoveryCodes() {
                    if (!window.recoveryCodes || window.recoveryCodes.length === 0) return;

                    const date = new Date().toISOString().split('T')[0];
                    const content = `TrackFlow Recovery Codes
                                                                                    ================================
                                                                                    Generated: ${date}

                                                                                    IMPORTANT: Store these codes in a safe place!
                                                                                    Each code can only be used once to access your account 
                                                                                    if you lose your authenticator device.

                                                                                    Your Recovery Codes:
                                                                                    --------------------
                                                                                    ${window.recoveryCodes.map((code, i) => `${i + 1}. ${code}`).join('\n')}

                                                                                    ================================
                                                                                    Keep these codes secure and private.
                                                                                    Do not share them with anyone.`;

                    const blob = new Blob([content], { type: 'text/plain' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `trackflow-recovery-codes-${date}.txt`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                }

                window.copyRecoveryCodes = function copyRecoveryCodes() {
                    if (!window.recoveryCodes || window.recoveryCodes.length === 0) return;

                    const codesText = window.recoveryCodes.join('\n');

                    navigator.clipboard.writeText(codesText).then(() => {
                        popupSuccess('Recovery codes copied to clipboard!', 'Copied');
                    }).catch(err => {
                        console.error('Failed to copy:', err);
                        popupError('Failed to copy codes. Please try manually.', 'Error');
                    });
                }

                // Theme Settings
                async function setTheme(theme) {
                    try {
                        const response = await fetch('/settings/preferences', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ theme })
                        });

                        const data = await response.json();

                        if (data.success) {
                            popupSuccess('Theme updated successfully', 'Success');

                            // Update UI to show active theme
                            ['themeLight', 'themeDark', 'themeAuto'].forEach(id => {
                                const element = document.getElementById(id);
                                if (element) {
                                    element.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                                    element.classList.add('border-gray-300', 'dark:border-gray-600');
                                }
                            });
                            const activeThemeBtn = document.getElementById('theme' + theme.charAt(0).toUpperCase() +
                                theme.slice(1));
                            if (activeThemeBtn) {
                                activeThemeBtn.classList.remove('border-gray-300', 'dark:border-gray-600');
                                activeThemeBtn.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                            }

                            // Save theme preference
                            localStorage.setItem('theme', theme);

                            // Apply theme immediately
                            if (theme === 'dark') {
                                document.documentElement.classList.add('dark');
                            } else if (theme === 'light') {
                                document.documentElement.classList.remove('dark');
                            } else if (theme === 'auto') {
                                // Auto mode - check system preference with fallback
                                try {
                                    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                                    // Remove dark class first
                                    document.documentElement.classList.remove('dark');
                                    // Only add if explicitly true
                                    if (mediaQuery && mediaQuery.matches === true) {
                                        document.documentElement.classList.add('dark');
                                    }
                                } catch (e) {
                                    // Fallback to light mode
                                    document.documentElement.classList.remove('dark');
                                }
                            }

                            // Update the global theme variable
                            window.currentTheme = theme;
                        } else {
                            popupError(data.message, 'Error');
                        }
                    } catch (error) {
                        popupError('Failed to update theme', 'Error');
                        console.error('Theme update error:', error);
                    }
                }

                // Toggle Preference
                async function togglePreference(key, value) {
                    try {
                        const response = await fetch('/settings/preferences', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ [key]: value })
                        });

                        const data = await response.json();

                        if (data.success) {
                            popupSuccess('Preference updated', 'Success');
                        } else {
                            popupError(data.message, 'Error');
                        }
                    } catch (error) {
                        popupError('Failed to update preference', 'Error');
                        console.error('Preference update error:', error);
                    }
                }

                // Change Language
                async function changeLanguage() {
                    const language = document.getElementById('languageSelect').value;

                    try {
                        const response = await fetch('/settings/preferences', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ language })
                        });

                        const data = await response.json();

                        if (data.success) {
                            popupSuccess('Language updated successfully', 'Success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            popupError(data.message, 'Error');
                        }
                    } catch (error) {
                        popupError('Failed to update language', 'Error');
                        console.error('Language update error:', error);
                    }
                }

                // Change Date Format
                async function changeDateFormat() {
                    const dateFormat = document.getElementById('dateFormatSelect').value;

                    try {
                        const response = await fetch('/settings/preferences', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ date_format: dateFormat })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update global date format
                            window.AppDateFormat = dateFormat;

                            popupSuccess('Date format updated successfully', 'Success');

                            // Reload page after a short delay to apply new format everywhere
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            popupError(data.message, 'Error');
                        }
                    } catch (error) {
                        popupError('Failed to update date format', 'Error');
                        console.error('Date format update error:', error);
                    }
                }

                // Toggle Notification with loading state and bottom-right toast
                window.toggleNotification = async function (key, value, inputElement) {
                    console.log('toggleNotification called:', key, value);
                    const loader = document.getElementById(`loader-${key}`);
                    console.log('Loader element:', loader);

                    // Show loading spinner
                    if (loader) {
                        loader.classList.remove('hidden');
                        console.log('Loader shown');
                    }

                    // Disable the toggle during save
                    if (inputElement) {
                        inputElement.disabled = true;
                    }

                    try {
                        console.log('Sending request to /settings/notifications');
                        const response = await fetch('/settings/notifications', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ [key]: value ? 1 : 0 })
                        });

                        console.log('Response status:', response.status);
                        const data = await response.json();
                        console.log('Response data:', data);

                        if (data.success) {
                            // Show bottom-right toast notification
                            console.log('Success - showing toast');
                            showNotificationToast(value ? 'Notification enabled' : 'Notification disabled', 'success');
                        } else {
                            // Revert the toggle state on error
                            console.log('Failed - reverting toggle');
                            if (inputElement) {
                                inputElement.checked = !value;
                            }
                            showNotificationToast(data.message || 'Failed to update', 'error');
                        }
                    } catch (error) {
                        // Revert the toggle state on error
                        console.error('Error caught:', error);
                        if (inputElement) {
                            inputElement.checked = !value;
                        }
                        showNotificationToast('Failed to update notification', 'error');
                        console.error('Notification update error:', error);
                    } finally {
                        // Hide loading spinner
                        if (loader) {
                            loader.classList.add('hidden');
                        }

                        // Re-enable the toggle
                        if (inputElement) {
                            inputElement.disabled = false;
                        }
                    }
                }

                // Bottom-right toast notification for notification settings
                window.showNotificationToast = function (message, type = 'success') {
                    // Create toast container if it doesn't exist
                    let toastContainer = document.getElementById('notification-toast-container');
                    if (!toastContainer) {
                        toastContainer = document.createElement('div');
                        toastContainer.id = 'notification-toast-container';
                        toastContainer.className = 'fixed bottom-4 right-4 z-[9999] flex flex-col gap-2';
                        toastContainer.style.cssText = 'pointer-events: none;';
                        document.body.appendChild(toastContainer);
                    }

                    // Create toast element
                    const toast = document.createElement('div');
                    toast.style.cssText = 'pointer-events: auto; opacity: 0; transform: translateX(100%); transition: all 0.3s ease-out;';
                    toast.className = `flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl ${type === 'success'
                        ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white'
                        : 'bg-gradient-to-r from-red-500 to-rose-600 text-white'
                        }`;

                    const icon = type === 'success'
                        ? '<i class="fas fa-check-circle text-lg"></i>'
                        : '<i class="fas fa-exclamation-circle text-lg"></i>';

                    toast.innerHTML = `
                                                                                            ${icon}
                                                                                            <span class="font-medium text-sm">${message}</span>
                                                                                        `;

                    toastContainer.appendChild(toast);

                    // Animate in after a tiny delay
                    setTimeout(() => {
                        toast.style.opacity = '1';
                        toast.style.transform = 'translateX(0)';
                    }, 10);

                    // Auto dismiss after 2.5 seconds
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            if (toast.parentNode) {
                                toast.remove();
                            }
                            // Remove container if empty
                            if (toastContainer && toastContainer.children.length === 0) {
                                toastContainer.remove();
                            }
                        }, 300);
                    }, 2500);
                }

                // Update Large Transaction Threshold
                window.updateLargeTransactionThreshold = async function () {
                    const thresholdEl = document.getElementById('notifThreshold');
                    if (!thresholdEl) return;
                    const threshold = thresholdEl.value;
                    const saveBtn = thresholdEl.closest('.flex').querySelector('button');

                    if (!threshold || isNaN(threshold) || parseFloat(threshold) < 0) {
                        showNotificationToast('Please enter a valid threshold amount', 'error');
                        return;
                    }

                    // Show loading state on button
                    if (saveBtn) {
                        saveBtn.disabled = true;
                        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                    }

                    try {
                        const response = await fetch('/settings/notifications', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ large_transaction_threshold: parseFloat(threshold) })
                        });

                        const data = await response.json();
                        if (response.ok && data.success) {
                            showNotificationToast('Threshold updated successfully', 'success');
                        } else {
                            showNotificationToast(data.message || 'Failed to update threshold', 'error');
                        }
                    } catch (error) {
                        showNotificationToast('Failed to update threshold', 'error');
                        console.error('Threshold update error:', error);
                    } finally {
                        // Restore button state
                        if (saveBtn) {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = '<i class="fas fa-check"></i> Save';
                        }
                    }
                }

                // Simple toast wrapper that reuses existing popup notification helpers
                function showToast(titleOrOptions, message, type = 'info') {
                    // Handle object argument format: showToast({ message: ..., type: ... })
                    let msg, msgType;
                    if (typeof titleOrOptions === 'object' && titleOrOptions !== null) {
                        msg = titleOrOptions.message || '';
                        msgType = titleOrOptions.type || 'info';
                    } else {
                        // Handle positional arguments: showToast(title, message, type)
                        msg = message || titleOrOptions;
                        msgType = type;
                    }

                    // Use popupSuccess/popupError which are defined and working
                    if (msgType === 'success' && typeof popupSuccess === 'function') {
                        popupSuccess(msg, 'Success');
                    } else if (msgType === 'error' && typeof popupError === 'function') {
                        popupError(msg, 'Error');
                    } else if (typeof popupSuccess === 'function') {
                        popupSuccess(msg, 'Info');
                    }
                }

                // Profile picture functions are defined at the top of the file
                // No need to redefine them here

                // Session Management Functions - Simple and Reliable
                window.revokeSession = async function revokeSession(sessionId) {
                    if (!confirm('Are you sure you want to revoke this session? The user will be logged out from that device.')) {
                        return;
                    }

                    // Show loading state on the button
                    const sessionCard = document.querySelector(`[data-session-id="${sessionId}"]`);
                    if (sessionCard) {
                        const btn = sessionCard.querySelector('button');
                        if (btn) {
                            btn.disabled = true;
                            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Revoking...';
                        }
                    }

                    try {
                        const response = await fetch(`/settings/sessions/${sessionId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Remove the session card with animation
                            if (sessionCard) {
                                sessionCard.style.transition = 'all 0.3s ease';
                                sessionCard.style.opacity = '0';
                                sessionCard.style.transform = 'translateX(20px)';
                                setTimeout(() => {
                                    sessionCard.remove();
                                    // Update session count in header
                                    updateSessionCount();
                                }, 300);
                            }
                            if (typeof popupSuccess === 'function') {
                                popupSuccess('Session revoked successfully! The device has been logged out.', 'Success');
                            }
                        } else {
                            if (typeof popupError === 'function') {
                                popupError(data.message || 'Failed to revoke session', 'Error');
                            }
                            // Reset button
                            if (sessionCard) {
                                const btn = sessionCard.querySelector('button');
                                if (btn) {
                                    btn.disabled = false;
                                    btn.innerHTML = '<i class="fas fa-sign-out-alt text-xs"></i> Revoke';
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Failed to revoke session:', error);
                        if (typeof popupError === 'function') {
                            popupError('Failed to revoke session. Please try again.', 'Error');
                        }
                        // Reset button
                        if (sessionCard) {
                            const btn = sessionCard.querySelector('button');
                            if (btn) {
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fas fa-sign-out-alt text-xs"></i> Revoke';
                            }
                        }
                    }
                }

                window.revokeAllOtherSessions = async function revokeAllOtherSessions() {
                    if (!confirm('Are you sure you want to revoke all other sessions? This will log you out from all other devices.')) {
                        return;
                    }

                    try {
                        const response = await fetch('/settings/sessions/revoke-all', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        });

                        const data = await response.json();

                        if (data.success) {
                            if (typeof popupSuccess === 'function') {
                                popupSuccess('All other sessions have been revoked!', 'Success');
                            }
                            // Reload page to show updated sessions
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            if (typeof popupError === 'function') {
                                popupError(data.message || 'Failed to revoke sessions', 'Error');
                            }
                        }
                    } catch (error) {
                        console.error('Failed to revoke sessions:', error);
                        if (typeof popupError === 'function') {
                            popupError('Failed to revoke sessions. Please try again.', 'Error');
                        }
                    }
                }

                window.updateSessionCount = function updateSessionCount() {
                    const container = document.getElementById('sessionsContainer');
                    const sessionCards = container ? container.querySelectorAll('.session-card') : [];
                    const count = sessionCards.length;

                    // If no sessions left, show empty state
                    if (count === 0) {
                        container.innerHTML = `
                                                                                                        <div class="flex flex-col items-center justify-center p-12 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/30 dark:to-gray-800/30 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                                                                                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                                                                                                <i class="fas fa-desktop text-3xl text-gray-400 dark:text-gray-500"></i>
                                                                                                            </div>
                                                                                                            <p class="text-base text-gray-600 dark:text-gray-400 font-medium">No active sessions found</p>
                                                                                                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Your session history will appear here</p>
                                                                                                        </div>
                                                                                                    `;
                    }
                }

                // Auto-save for toggles
                document.addEventListener('DOMContentLoaded', function () {
                    // Initialize theme buttons based on current theme
                    const currentTheme = localStorage.getItem('theme') || 'auto';
                    ['themeLight', 'themeDark', 'themeAuto'].forEach(id => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                            element.classList.add('border-gray-300', 'dark:border-gray-600');
                        }
                    });
                    const activeThemeBtn = document.getElementById('theme' + currentTheme.charAt(0).toUpperCase() + currentTheme.slice(1));
                    if (activeThemeBtn) {
                        activeThemeBtn.classList.remove('border-gray-300', 'dark:border-gray-600');
                        activeThemeBtn.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                    }

                    // Sessions are now loaded server-side, no need to call loadActiveSessions()

                    // Add change listeners to notification toggles
                    const notifToggles = [
                        'notifEmailTransactions',
                        'notifEmailBudgets',
                        'notifEmailWeekly',
                        'notifPushLarge',
                        'notifPushGoals'
                    ];

                    notifToggles.forEach(id => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.addEventListener('change', updateNotifications);
                        }
                    });

                    // Add change listeners to preference toggles
                    const prefToggles = ['prefCompactMode', 'prefShowDecimals'];
                    prefToggles.forEach(id => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.addEventListener('change', updatePreferences);
                        }
                    });
                });

            </script>

            <style>
                @keyframes slide-in {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }

                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }

                .animate-slide-in {
                    animation: slide-in 0.3s ease-out;
                    transition: all 0.3s ease-out;
                }

                #otpNotification {
                    position: fixed;
                    top: 80px;
                    right: 20px;
                    z-index: 9999;
                    max-width: 400px;
                }
            </style>
        @endpush

        <!-- Delete Account Modal -->
        <div id="deleteAccountModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" onclick="closeDeleteAccountModal()"></div>
            <div
                class="relative max-w-lg w-full bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-lg shadow-lg border border-white/50 dark:border-gray-700/50-lg p-6 mx-4">
                <div id="deleteConfirmSection">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Account</h3>
                        <button onclick="closeDeleteAccountModal()"
                            class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">This action will permanently delete your
                        account
                        and all associated data. To confirm, click "Confirm It" and you will receive an 8-digit OTP via
                        email to
                        complete the deletion.</p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button onclick="closeDeleteAccountModal()"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">Cancel</button>
                        <button id="deleteConfirmBtn" onclick="startDeleteAccountFlow()"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">Confirm It</button>
                    </div>
                </div>

                <div id="deleteOtpSection" class="hidden">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Enter OTP</h3>
                        <button onclick="closeDeleteAccountModal()"
                            class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">An 8-digit OTP has been sent to your
                        registered
                        email and the notification center. Enter it below to permanently delete your account.</p>

                    <div class="mt-4 flex items-center justify-center gap-1 sm:gap-2 flex-wrap">
                        <input maxlength="1"
                            oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value) { const n=this.nextElementSibling; if(n) n.focus(); } updateDeleteHiddenOtp();"
                            onkeydown="if(event.key==='Backspace'&&!this.value&&this.previousElementSibling) this.previousElementSibling.focus();"
                            class="otp-input w-9 h-10 sm:w-10 sm:h-11 md:w-12 md:h-12 text-center text-lg sm:text-xl font-bold rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" />
                        <input maxlength="1"
                            oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value) { const n=this.nextElementSibling; if(n) n.focus(); } updateDeleteHiddenOtp();"
                            onkeydown="if(event.key==='Backspace'&&!this.value&&this.previousElementSibling) this.previousElementSibling.focus();"
                            class="otp-input w-9 h-10 sm:w-10 sm:h-11 md:w-12 md:h-12 text-center text-lg sm:text-xl font-bold rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" />
                        <input maxlength="1"
                            oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value) { const n=this.nextElementSibling; if(n) n.focus(); } updateDeleteHiddenOtp();"
                            onkeydown="if(event.key==='Backspace'&&!this.value&&this.previousElementSibling) this.previousElementSibling.focus();"
                            class="otp-input w-9 h-10 sm:w-10 sm:h-11 md:w-12 md:h-12 text-center text-lg sm:text-xl font-bold rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" />
                        <input maxlength="1"
                            oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value) { const n=this.nextElementSibling; if(n) n.focus(); } updateDeleteHiddenOtp();"
                            onkeydown="if(event.key==='Backspace'&&!this.value&&this.previousElementSibling) this.previousElementSibling.focus();"
                            class="otp-input w-9 h-10 sm:w-10 sm:h-11 md:w-12 md:h-12 text-center text-lg sm:text-xl font-bold rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" />
                        <input maxlength="1"
                            oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value) { const n=this.nextElementSibling; if(n) n.focus(); } updateDeleteHiddenOtp();"
                            onkeydown="if(event.key==='Backspace'&&!this.value&&this.previousElementSibling) this.previousElementSibling.focus();"
                            class="otp-input w-9 h-10 sm:w-10 sm:h-11 md:w-12 md:h-12 text-center text-lg sm:text-xl font-bold rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" />
                        <input maxlength="1"
                            oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value) { const n=this.nextElementSibling; if(n) n.focus(); } updateDeleteHiddenOtp();"
                            onkeydown="if(event.key==='Backspace'&&!this.value&&this.previousElementSibling) this.previousElementSibling.focus();"
                            class="otp-input w-9 h-10 sm:w-10 sm:h-11 md:w-12 md:h-12 text-center text-lg sm:text-xl font-bold rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" />
                        <input maxlength="1"
                            oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value) { const n=this.nextElementSibling; if(n) n.focus(); } updateDeleteHiddenOtp();"
                            onkeydown="if(event.key==='Backspace'&&!this.value&&this.previousElementSibling) this.previousElementSibling.focus();"
                            class="otp-input w-9 h-10 sm:w-10 sm:h-11 md:w-12 md:h-12 text-center text-lg sm:text-xl font-bold rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" />
                        <input maxlength="1"
                            oninput="this.value=this.value.replace(/[^0-9]/g,''); updateDeleteHiddenOtp();"
                            onkeydown="if(event.key==='Backspace'&&!this.value&&this.previousElementSibling) this.previousElementSibling.focus();"
                            class="otp-input w-9 h-10 sm:w-10 sm:h-11 md:w-12 md:h-12 text-center text-lg sm:text-xl font-bold rounded-lg border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" />
                    </div>
                    <input type="hidden" id="deleteOtpHidden" value="" />

                    <div class="mt-6 flex justify-end gap-3">
                        <button onclick="closeDeleteAccountModal()"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md">Cancel</button>
                        <button onclick="verifyDeleteAccountOtp()"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">Verify & Delete</button>
                    </div>

                    <div id="deleteSuccessTick" class="hidden mt-4 text-green-600 text-2xl text-center">&#10004; Account
                        deleted
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Account Functions - Must be right after modal -->
        <script>
            window.showDeleteConfirm = function () {
                const modal = document.getElementById('deleteAccountModal');
                if (modal) modal.classList.remove('hidden');
            };

            window.closeDeleteAccountModal = function () {
                const modal = document.getElementById('deleteAccountModal');
                if (modal) modal.classList.add('hidden');
            };

            window.startDeleteAccountFlow = async function () {
                try {
                    const yesBtn = document.getElementById('deleteConfirmBtn');
                    if (yesBtn) {
                        yesBtn.disabled = true;
                        yesBtn.dataset.orig = yesBtn.textContent;
                        yesBtn.textContent = 'Sending...';
                    }

                    const response = await fetch('/settings/delete-account/send-otp', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        document.getElementById('deleteOtpSection').classList.remove('hidden');
                        document.getElementById('deleteConfirmSection').classList.add('hidden');

                        // Show toast notification in bottom-right corner
                        if (typeof toastOtp === 'function') {
                            toastOtp('{{ auth()->user()->email ?? "your email" }}');
                        }

                        if (typeof popupSuccess === 'function') {
                            popupSuccess('OTP sent to your registered email', 'Success');
                        }

                        const first = document.querySelector('#deleteOtpSection .otp-input');
                        if (first) first.focus();
                    } else {
                        if (typeof popupError === 'function') {
                            popupError(data.message || 'Failed to send OTP. Please try again.', 'Error');
                        }
                    }
                } catch (error) {
                    console.error('send delete otp error:', error);
                    if (typeof popupError === 'function') {
                        popupError('Failed to send OTP. Please try again.', 'Error');
                    }
                } finally {
                    const yesBtn = document.getElementById('deleteConfirmBtn');
                    if (yesBtn) {
                        yesBtn.disabled = false;
                        yesBtn.textContent = yesBtn.dataset.orig || 'Confirm It';
                    }
                }
            };

            window.updateDeleteHiddenOtp = function () {
                const inputs = Array.from(document.querySelectorAll('#deleteOtpSection .otp-input'));
                if (!inputs.length) return;
                document.getElementById('deleteOtpHidden').value = inputs.map(i => i.value).join('');
            };

            window.verifyDeleteAccountOtp = async function () {
                const code = document.getElementById('deleteOtpHidden').value || '';
                if (code.length !== 8) {
                    if (typeof popupError === 'function') {
                        popupError('Please enter the 8-digit OTP', 'Validation Error');
                    }
                    return;
                }
                try {
                    const response = await fetch('/settings/delete-account/verify', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ otp: code })
                    });
                    const data = await response.json();
                    if (response.ok && data.success) {
                        const okEl = document.getElementById('deleteSuccessTick');
                        if (okEl) okEl.classList.remove('hidden');
                        if (typeof popupSuccess === 'function') {
                            popupSuccess('Account deleted successfully. Redirecting...', 'Success');
                        }
                        setTimeout(() => { window.location.href = '/'; }, 2000);
                    } else {
                        if (typeof popupError === 'function') {
                            popupError(data.message || 'Invalid OTP', 'Error');
                        }
                    }
                } catch (error) {
                    console.error('verify delete otp error:', error);
                    if (typeof popupError === 'function') {
                        popupError('Verification failed. Please try again.', 'Error');
                    }
                }
            };
        </script>

        <!-- Ensure 2FA functions are globally available for all buttons -->
        <script>
            window.enable2FAApp = async function enable2FAApp() {
                try {
                    const response = await fetch('/settings/2fa/enable', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        document.getElementById('qrCode').innerHTML = data.qr_code;
                        document.getElementById('secretKey').textContent = data.secret;
                        document.getElementById('twoFactorModal').classList.remove('hidden');
                        document.getElementById('twoFactorStep1').classList.remove('hidden');
                        document.getElementById('twoFactorStep2').classList.add('hidden');
                    } else {
                        popupError(data.message || 'Failed to generate 2FA setup', 'Error');
                    }
                } catch (error) {
                    console.error('2FA enable error:', error);
                    popupError('Failed to enable 2FA. Please try again.', 'Error');
                }
            }
            window.enable2FAEmail = function enable2FAEmail() {
                document.getElementById('emailOtpModal').classList.remove('hidden');
                document.getElementById('emailOtpStep1').classList.remove('hidden');
                document.getElementById('emailOtpStep2').classList.add('hidden');
            }
        </script>

        <!-- Temporary client-side debug helper: call from browser console with `debugSession()` -->
        <script>
            window.debugSession = async function debugSession() {
                try {
                    console.log('Calling /debug/session...');
                    const resp = await fetch('/debug/session', {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await resp.json();
                    console.log('/debug/session response status:', resp.status);
                    console.log('/debug/session body:', data);
                    alert('Debug session: check console for details');
                    return data;
                } catch (err) {
                    console.error('debugSession failed', err);
                    alert('debugSession failed - see console');
                    throw err;
                }
            }
        </script>
        @push('scripts')
            <script>
                if (video && video.srcObject) {
                    video.srcObject.getTracks().forEach(track => track.stop());
                    video.srcObject = null;
                }
                // Hide modal
                document.getElementById('faceModal').classList.add('hidden');
                // Reset status and progress
                document.getElementById('faceModalMsg').innerText = '';
                document.getElementById('faceProgress').style.width = '0%';
                document.getElementById('faceCheckmark').classList.add('hidden');
                                        }
                // Face capture logic

                // --- Robust Face Detection with Tuned Options ---
                let faceStream = null;
                let captureDone = false;
                let stableFrames = 0;
                let lastBox = null;
                const REQUIRED_STABLE_FRAMES = 12; // ~3 seconds
                const detectorOptions = new faceapi.TinyFaceDetectorOptions({
                    inputSize: 416,
                    scoreThreshold: 0.4
                });

                async function startCamera() {
                    const video = document.getElementById('faceVideo');
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: { ideal: 640 },
                            height: { ideal: 480 }
                        }
                    });
                    video.srcObject = stream;
                    return new Promise(resolve => {
                        video.onloadedmetadata = () => {
                            video.play();
                            resolve(video);
                        };
                    });
                }

                async function startFaceCapture(video, isReEnroll = false) {
                    captureDone = false;
                    stableFrames = 0;
                    lastBox = null;
                    updateProgress(0);
                    document.getElementById('faceCheckmark').classList.add('hidden');
                    video.style.display = 'block';
                    video.style.visibility = 'visible';
                    if (!(location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1')) {
                        showStatus('Camera access requires HTTPS or localhost. Please use https:// or http://localhost.');
                        return;
                    }
                    try {
                        await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
                        await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                        await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
                    } catch (e) {
                        showStatus('Failed to load face-api.js models.');
                        return;
                    }
                    startFaceDetection(video, isReEnroll);
                }

                function showStatus(text) {
                    document.getElementById('faceModalMsg').innerText = text;
                }

                function updateProgress(percent) {
                    document.getElementById('faceProgress').style.width = percent + '%';
                }

                function showFaceCheckmark() {
                    document.getElementById('faceCheckmark').classList.remove('hidden');
                }

                function normalizeVector(vector) {
                    // ...existing code...
                    const norm = Math.sqrt(vector.reduce((s, v) => s + v * v, 0));
                    return vector.map(v => Number((v / norm).toFixed(6)));
                }

                async function startFaceDetection(video, isReEnroll) {
                    const interval = setInterval(async () => {
                        if (captureDone) return;
                        // CRITICAL GUARD: Wait for video to be ready
                        if (video.videoWidth === 0 || video.videoHeight === 0) {
                            return;
                        }
                        const detection = await faceapi
                            .detectSingleFace(video)
                            .withFaceLandmarks()
                            .withFaceDescriptor();

                        if (!detection) {
                            stableFrames = 0;
                            lastBox = null;
                            updateProgress(0);
                            showStatus('Looking for face...');
                            return;
                        }

                        showStatus('Face detected. Hold still…');
                        const box = detection.detection.box;
                        if (lastBox) {
                            const dx = Math.abs(box.x - lastBox.x);
                            const dy = Math.abs(box.y - lastBox.y);
                            if (dx < 15 && dy < 15) {
                                stableFrames++;
                            } else {
                                stableFrames = 0;
                            }
                        }
                        lastBox = box;
                        const progress = Math.min((stableFrames / REQUIRED_STABLE_FRAMES) * 100, 100);
                        updateProgress(progress);
                        if (stableFrames >= REQUIRED_STABLE_FRAMES) {
                            captureDone = true;
                            clearInterval(interval);
                            await captureAndStoreFace(detection.descriptor, isReEnroll);
                        }
                    }, 250);
                }

                async function captureAndStoreFace(descriptor, isReEnroll) {
                    // Stop camera
                    const video = document.getElementById('faceVideo');
                    if (faceStream) faceStream.getTracks().forEach(track => track.stop());
                    video.srcObject = null;
                    showFaceCheckmark();
                    // Normalize vector (IMPORTANT)
                    const vector = normalizeVector(Array.from(descriptor));
                    showFaceSuccessModal();
                    const okBtn = document.getElementById('faceSuccessOkBtn');
                    // Store the normalized vector globally so the OK button handler can send it later
                    window._lastCapturedFaceVector = vector;
                    // Update modal message to instruct user
                    showStatus('Face captured. Click OK to save.');
                    // We rely on the centralized OK handler added on DOMContentLoaded
                    if (okBtn) {
                        // No-op here; clicking OK will call sendEnrolledFaceVectorFromWindow()
                    }
                }
                // Show and close success modal helpers
                function showFaceSuccessModal() {
                    // Hide the face capture modal if visible
                    var captureModal = document.getElementById('faceModal');
                    if (captureModal) captureModal.classList.add('hidden');
                    // Show the success modal
                    var successModal = document.getElementById('faceSuccessModal');
                    if (successModal) successModal.classList.remove('hidden');
                }
                function closeFaceSuccessModal() {
                    var successModal = document.getElementById('faceSuccessModal');
                    if (successModal) successModal.classList.add('hidden');
                    closeFaceModal();
                }

                function updateProgress(percent) {
                    document.getElementById('faceProgress').style.width = percent + '%';
                }

                function showFaceCheckmark() {
                    document.getElementById('faceCheckmark').classList.remove('hidden');
                }

                function normalizeVector(vector) {
                    const norm = Math.sqrt(vector.reduce((s, v) => s + v * v, 0));
                    return vector.map(v => Number((v / norm).toFixed(6)));
                }

                // Theme Setting Function
                async function setTheme(theme) {
                    try {
                        const response = await fetch('/settings/update-theme', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ theme })
                        });

                        const data = await response.json();

                        if (data.success) {
                            popupSuccess('Theme updated successfully!', 'Success');
                            setTimeout(() => window.location.reload(), 500);
                        } else {
                            popupError(data.message || 'Failed to update theme', 'Error');
                        }
                    } catch (error) {
                        popupError('Failed to update theme. Please try again.', 'Error');
                    }
                }

                // Save Preferences Function
                async function savePreferences() {
                    const compactMode = document.getElementById('compactMode').checked ? 1 : 0;
                    const showDecimals = document.getElementById('showDecimals').checked ? 1 : 0;
                    const language = document.getElementById('prefLanguage').value;
                    const dateFormat = document.getElementById('prefDateFormat').value;

                    try {
                        const response = await fetch('/settings/update-preferences', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ compact_mode: compactMode, show_decimals: showDecimals, language, date_format: dateFormat })
                        });

                        const data = await response.json();

                        if (data.success) {
                            popupSuccess('Preferences saved successfully!', 'Success');
                        } else {
                            popupError(data.message || 'Failed to save preferences', 'Error');
                        }
                    } catch (error) {
                        popupError('Failed to save preferences. Please try again.', 'Error');
                    }
                }


            </script>
        @endpush