// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar toggle
    initSidebarToggle();

    // Initialize form validation
    initFormValidation();

    // Initialize settings form
    initSettingsForm();

    // Initialize comments management
    initCommentsManagement();

    // Initialize tabs
    initTabs();

    // Initialize tooltips
    initTooltips();

    // Add animation effects
    addAnimationEffects();

    // Initialize appearance settings
    initAppearanceSettings();
});

/**
 * Initialize sidebar toggle functionality
 */
function initSidebarToggle() {
    const toggleButton = document.querySelector('.admin-header-toggle');
    const sidebar = document.querySelector('.admin-sidebar');

    if (toggleButton && sidebar) {
        toggleButton.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(event.target) && !toggleButton.contains(event.target) && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('active');
            }
        });
    }
}

/**
 * Initialize form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;

                    // Add error class
                    field.classList.add('admin-input-error');

                    // Create error message if it doesn't exist
                    let errorMessage = field.nextElementSibling;
                    if (!errorMessage || !errorMessage.classList.contains('admin-error-message')) {
                        errorMessage = document.createElement('div');
                        errorMessage.className = 'admin-error-message';
                        errorMessage.textContent = 'هذا الحقل مطلوب';
                        field.parentNode.insertBefore(errorMessage, field.nextSibling);
                    }
                } else {
                    // Remove error class
                    field.classList.remove('admin-input-error');

                    // Remove error message if it exists
                    const errorMessage = field.nextElementSibling;
                    if (errorMessage && errorMessage.classList.contains('admin-error-message')) {
                        errorMessage.remove();
                    }
                }
            });

            if (!isValid) {
                event.preventDefault();
            }
        });
    });
}

/**
 * Initialize settings form functionality
 */
function initSettingsForm() {
    const settingsForm = document.getElementById('settings-form');

    if (settingsForm) {
        // Add field for new feature
        const addFeatureButton = document.getElementById('add-feature');
        const featuresContainer = document.getElementById('features-container');

        if (addFeatureButton && featuresContainer) {
            addFeatureButton.addEventListener('click', function() {
                const featureIndex = document.querySelectorAll('.admin-feature-item').length;

                // Get the current language
                const langCode = document.querySelector('input[name="language_code"]')?.value || 'ar';
                const isArabic = langCode === 'ar';

                const featureItem = document.createElement('div');
                featureItem.className = 'admin-feature-item';
                featureItem.innerHTML = `
                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label for="feature-icon-${featureIndex}">${isArabic ? 'أيقونة الميزة' : 'Feature Icon'}</label>
                            <input type="text" id="feature-icon-${featureIndex}" name="features[${featureIndex}][icon]" placeholder="${isArabic ? 'مثال: fas fa-mobile-alt' : 'Example: fas fa-mobile-alt'}">
                        </div>
                        <div class="admin-form-col">
                            <label for="feature-title-${featureIndex}">${isArabic ? 'عنوان الميزة' : 'Feature Title'}</label>
                            <input type="text" id="feature-title-${featureIndex}" name="features[${featureIndex}][title]" placeholder="${isArabic ? 'عنوان الميزة' : 'Feature title'}">
                        </div>
                    </div>
                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label for="feature-description-${featureIndex}">${isArabic ? 'وصف الميزة' : 'Feature Description'}</label>
                            <input type="text" id="feature-description-${featureIndex}" name="features[${featureIndex}][description]" placeholder="${isArabic ? 'وصف الميزة' : 'Feature description'}">
                        </div>
                        <div class="admin-form-col admin-form-col-actions">
                            <button type="button" class="admin-button admin-button-danger remove-feature">${isArabic ? 'حذف' : 'Delete'}</button>
                        </div>
                    </div>
                `;

                featuresContainer.appendChild(featureItem);

                // Add event listener to remove button
                const removeButton = featureItem.querySelector('.remove-feature');
                removeButton.addEventListener('click', function() {
                    featureItem.remove();
                });
            });

            // Add event listeners to existing remove buttons
            document.querySelectorAll('.remove-feature').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.admin-feature-item').remove();
                });
            });
        }

        // Add field for new social link
        const addSocialButton = document.getElementById('add-social');
        const socialContainer = document.getElementById('social-container');

        if (addSocialButton && socialContainer) {
            addSocialButton.addEventListener('click', function() {
                const socialIndex = document.querySelectorAll('.admin-social-item').length;

                // Get the current language
                const langCode = document.querySelector('input[name="language_code"]')?.value || 'ar';
                const isArabic = langCode === 'ar';

                const socialItem = document.createElement('div');
                socialItem.className = 'admin-social-item';
                socialItem.innerHTML = `
                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label for="social-icon-${socialIndex}">${isArabic ? 'أيقونة' : 'Icon'}</label>
                            <input type="text" id="social-icon-${socialIndex}" name="social[${socialIndex}][icon]" placeholder="${isArabic ? 'مثال: fab fa-facebook-f' : 'Example: fab fa-facebook-f'}">
                        </div>
                        <div class="admin-form-col">
                            <label for="social-url-${socialIndex}">${isArabic ? 'الرابط' : 'URL'}</label>
                            <input type="text" id="social-url-${socialIndex}" name="social[${socialIndex}][url]" placeholder="https://example.com">
                        </div>
                        <div class="admin-form-col admin-form-col-actions">
                            <button type="button" class="admin-button admin-button-danger remove-social">${isArabic ? 'حذف' : 'Delete'}</button>
                        </div>
                    </div>
                `;

                socialContainer.appendChild(socialItem);

                // Add event listener to remove button
                const removeButton = socialItem.querySelector('.remove-social');
                removeButton.addEventListener('click', function() {
                    socialItem.remove();
                });
            });

            // Add event listeners to existing remove buttons
            document.querySelectorAll('.remove-social').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.admin-social-item').remove();
                });
            });
        }
    }
}

/**
 * Initialize comments management functionality
 */
function initCommentsManagement() {
    const commentsTable = document.getElementById('comments-table');

    if (commentsTable) {
        // Delete comment
        const deleteButtons = document.querySelectorAll('.delete-comment');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                // Get the language from HTML tag
                const htmlLang = document.documentElement.lang || 'ar';
                const confirmMessage = htmlLang === 'ar' ? 'هل أنت متأكد من حذف هذا التعليق؟' : 'Are you sure you want to delete this comment?';

                if (confirm(confirmMessage)) {
                    const commentId = this.dataset.id;
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'comments.php';

                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'delete_comment';
                    idInput.value = commentId;

                    form.appendChild(idInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Toggle comment verification
        const verifyButtons = document.querySelectorAll('.verify-comment');

        verifyButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                const commentId = this.dataset.id;
                const isVerified = this.dataset.verified === 'true';
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'comments.php';

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'toggle_verify';
                idInput.value = commentId;

                const verifiedInput = document.createElement('input');
                verifiedInput.type = 'hidden';
                verifiedInput.name = 'verified';
                verifiedInput.value = isVerified ? '0' : '1';

                form.appendChild(idInput);
                form.appendChild(verifiedInput);
                document.body.appendChild(form);
                form.submit();
            });
        });
    }
}

/**
 * Initialize tabs functionality
 */
function initTabs() {
    const tabsContainers = document.querySelectorAll('.admin-tabs');

    tabsContainers.forEach(container => {
        const tabButtons = container.querySelectorAll('.admin-tab-button');
        const tabPanes = container.querySelectorAll('.admin-tab-pane');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.dataset.tab;

                // Remove active class from all buttons and panes
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));

                // Add active class to current button and pane
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');

                // Save active tab to localStorage
                if (tabId) {
                    localStorage.setItem('activeAdminTab', tabId);
                }
            });
        });

        // Check if there's a saved active tab
        const activeTab = localStorage.getItem('activeAdminTab');
        if (activeTab) {
            const activeButton = container.querySelector(`.admin-tab-button[data-tab="${activeTab}"]`);
            const activePane = document.getElementById(activeTab);

            if (activeButton && activePane) {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));

                activeButton.classList.add('active');
                activePane.classList.add('active');
            }
        }
    });
}

/**
 * Initialize tooltips
 */
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');

    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltipText = this.dataset.tooltip;

            if (!tooltipText) return;

            const tooltip = document.createElement('div');
            tooltip.className = 'admin-tooltip';
            tooltip.textContent = tooltipText;

            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();

            tooltip.style.top = (rect.top - tooltipRect.height - 10) + 'px';
            tooltip.style.left = (rect.left + (rect.width / 2) - (tooltipRect.width / 2)) + 'px';
            tooltip.style.opacity = '1';

            this.addEventListener('mouseleave', function() {
                tooltip.remove();
            }, { once: true });
        });
    });
}

/**
 * Add animation effects to elements
 */
function addAnimationEffects() {
    // Add fade-in animation to content sections
    const contentSections = document.querySelectorAll('.admin-form-section, .admin-stat-card, .admin-action-card');

    if (contentSections.length > 0) {
        contentSections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

            setTimeout(() => {
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, 100 + (index * 100));
        });
    }

    // Add hover effects to buttons and cards
    const hoverElements = document.querySelectorAll('.admin-button, .admin-stat-card, .admin-action-card');

    hoverElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
        });

        element.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
}

/**
 * Initialize appearance settings functionality
 */
function initAppearanceSettings() {
    // Handle color picker inputs
    const colorInputs = document.querySelectorAll('input[type="color"]');

    colorInputs.forEach(colorInput => {
        const textInput = colorInput.nextElementSibling;

        // Update text input when color input changes
        colorInput.addEventListener('input', function() {
            textInput.value = this.value;
        });

        // Update color input when text input changes
        textInput.addEventListener('input', function() {
            colorInput.value = this.value;
        });
    });

    // Handle image path inputs
    const imagePathInputs = {
        'product-image': 'product-image-preview',
        'favicon': 'favicon-preview',
        'touch-icon': 'touch-icon-preview',
        'og-image': 'og-image-preview',
        'twitter-image': 'twitter-image-preview'
    };

    for (const [inputId, previewId] of Object.entries(imagePathInputs)) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input && preview) {
            input.addEventListener('input', function() {
                // Update image preview when path changes
                const path = this.value;
                if (path) {
                    preview.src = '../' + path;
                }
            });
        }
    }
}
