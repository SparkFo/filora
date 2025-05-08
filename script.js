// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load site settings first
    loadSiteSettings().then(() => {
        // Initialize countdown timers after settings are loaded
        initCountdownTimers();

        // Load dynamic comments
        loadComments();

        // Add click tracking for CTA buttons
        trackCTAClicks();

        // Initialize mobile menu
        initMobileMenu();
    }).catch(error => {
        console.error('Error loading site settings:', error);

        // Initialize with default settings if there's an error
        initCountdownTimers();
        loadComments();
        trackCTAClicks();
        initMobileMenu();
    });
});

// Global settings object
let siteSettings = {
    general: {
        countdownDays: 3
    },
    appearance: {
        buttonColor: '#0071e3',
        backgroundColor: '#f5f5f7',
        textColor: '#1d1d1f'
    }
};

/**
 * Load site settings from JSON file
 * @returns {Promise} - Promise that resolves when settings are loaded
 */
function loadSiteSettings() {
    return new Promise((resolve, reject) => {
        fetch('site-settings.json')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load site settings');
                }
                return response.json();
            })
            .then(settings => {
                // Store settings globally
                siteSettings = settings;

                // Apply appearance settings
                applyAppearanceSettings(settings);

                resolve(settings);
            })
            .catch(error => {
                console.error('Error loading site settings:', error);
                reject(error);
            });
    });
}

/**
 * Apply appearance settings from the loaded settings
 * @param {Object} settings - The site settings object
 */
function applyAppearanceSettings(settings) {
    if (settings.appearance) {
        // Create a style element
        const styleEl = document.createElement('style');
        styleEl.textContent = `
            :root {
                --primary-color: ${settings.appearance.primaryColor || '#0071e3'};
                --secondary-color: ${settings.appearance.secondaryColor || '#2997ff'};
                --button-color: ${settings.appearance.buttonColor || '#0071e3'};
                --background-color: ${settings.appearance.backgroundColor || '#f5f5f7'};
                --text-color: ${settings.appearance.textColor || '#1d1d1f'};
            }

            body {
                background-color: var(--background-color);
                color: var(--text-color);
            }

            .cta-button-rect {
                background-color: var(--button-color);
            }
        `;
        document.head.appendChild(styleEl);
    }
}

/**
 * Initialize countdown timers
 * Sets countdowns for both timer instances
 */
function initCountdownTimers() {
    // Try to get saved end date from localStorage
    let countdownDate;
    const savedEndDate = localStorage.getItem('countdownEndDate');

    if (savedEndDate) {
        // Use the saved end date
        countdownDate = new Date(parseInt(savedEndDate));

        // If the saved date is in the past or invalid, create a new one
        if (isNaN(countdownDate) || countdownDate <= new Date()) {
            countdownDate = createNewEndDate();
        }
    } else {
        // Create and save a new end date
        countdownDate = createNewEndDate();
    }

    // Update the countdown every second
    const countdownTimer = setInterval(function() {
        // Get current date and time
        const now = new Date().getTime();

        // Calculate the time remaining
        const distance = countdownDate.getTime() - now;

        // Calculate days, hours, minutes, and seconds
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Display the countdown in both timer instances
        updateCountdownDisplay('days', 'hours', 'minutes', 'seconds', days, hours, minutes, seconds, distance);
        updateCountdownDisplay('days-cta', 'hours-cta', 'minutes-cta', 'seconds-cta', days, hours, minutes, seconds, distance);

        // If countdown is finished, create a new end date
        if (distance < 0) {
            countdownDate = createNewEndDate();
        }
    }, 1000);
}

/**
 * Create a new end date (3 days from now) and save it to localStorage
 * @returns {Date} - The new end date
 */
function createNewEndDate() {
    // Get countdown days from site settings or use default (3 days)
    let countdownDays = 3;

    // Use the global settings if available
    if (siteSettings && siteSettings.general && siteSettings.general.countdownDays) {
        countdownDays = parseInt(siteSettings.general.countdownDays);
    }

    console.log('Using countdown days:', countdownDays);

    // Set the countdown date based on settings
    const newEndDate = new Date();
    newEndDate.setDate(newEndDate.getDate() + countdownDays);

    // Save to localStorage
    localStorage.setItem('countdownEndDate', newEndDate.getTime().toString());

    return newEndDate;
}

/**
 * Update a specific countdown display
 * @param {string} daysId - ID of the days element
 * @param {string} hoursId - ID of the hours element
 * @param {string} minutesId - ID of the minutes element
 * @param {string} secondsId - ID of the seconds element
 * @param {number} days - Number of days remaining
 * @param {number} hours - Number of hours remaining
 * @param {number} minutes - Number of minutes remaining
 * @param {number} seconds - Number of seconds remaining
 * @param {number} distance - Total time remaining in milliseconds
 */
function updateCountdownDisplay(daysId, hoursId, minutesId, secondsId, days, hours, minutes, seconds, distance) {
    const daysElement = document.getElementById(daysId);
    const hoursElement = document.getElementById(hoursId);
    const minutesElement = document.getElementById(minutesId);
    const secondsElement = document.getElementById(secondsId);

    if (daysElement && hoursElement && minutesElement && secondsElement) {
        if (distance < 0) {
            // If the countdown is finished
            daysElement.textContent = '00';
            hoursElement.textContent = '00';
            minutesElement.textContent = '00';
            secondsElement.textContent = '00';

            // Find the parent countdown container and add expired message if not already there
            const countdownContainer = daysElement.closest('.countdown');
            if (countdownContainer && !countdownContainer.querySelector('.expired-text')) {
                const expiredText = document.createElement('h3');
                expiredText.className = 'expired-text';
                expiredText.textContent = 'انتهى العرض!';
                expiredText.style.color = 'var(--error-color)';
                expiredText.style.fontWeight = 'bold';
                expiredText.style.marginTop = '10px';
                countdownContainer.appendChild(expiredText);
            }
        } else {
            // Update the countdown display
            daysElement.textContent = formatTime(days);
            hoursElement.textContent = formatTime(hours);
            minutesElement.textContent = formatTime(minutes);
            secondsElement.textContent = formatTime(seconds);
        }
    }
}

/**
 * Format time to always show two digits
 * @param {number} time - The time value to format
 * @returns {string} - Formatted time with leading zero if needed
 */
function formatTime(time) {
    return time < 10 ? `0${time}` : time;
}

/**
 * Track clicks on CTA buttons
 * For CPA offers, tracking clicks is important
 */
function trackCTAClicks() {
    const ctaButtons = document.querySelectorAll('.cta-button-rect');

    ctaButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // In a real application, you would send this data to your analytics service
            console.log('CTA button clicked:', {
                buttonText: this.textContent,
                timestamp: new Date().toISOString(),
                location: window.location.href
            });

            // Add a new comment when the user clicks on a CTA button
            setTimeout(() => {
                addNewComment();
            }, 500);

            // You could also add a small delay before redirecting to ensure tracking is complete
            // For demo purposes, we're just logging to console and adding a comment
        });
    });

    // Load site settings from JSON file
    fetch('site-settings.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load site settings');
            }
            return response.json();
        })
        .then(settings => {
            // Update offer links with the one from settings
            if (settings.general && settings.general.offerLink) {
                ctaButtons.forEach(button => {
                    button.href = settings.general.offerLink;
                });
            }
        })
        .catch(error => {
            console.error('Error loading site settings:', error);
        });
}

/**
 * Initialize mobile menu functionality
 */
function initMobileMenu() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');

    if (mobileMenuToggle && mobileNav) {
        // Toggle mobile menu when clicking the hamburger icon
        mobileMenuToggle.addEventListener('click', function() {
            mobileNav.classList.toggle('active');

            // Change icon based on menu state
            const icon = this.querySelector('i');
            if (mobileNav.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close mobile menu when clicking on a link
        const mobileNavLinks = mobileNav.querySelectorAll('a');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileNav.classList.remove('active');

                // Reset icon
                const icon = mobileMenuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileNav.contains(event.target) && !mobileMenuToggle.contains(event.target) && mobileNav.classList.contains('active')) {
                mobileNav.classList.remove('active');

                // Reset icon
                const icon = mobileMenuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
}

/**
 * Load dynamic comments from comments.json file
 */
function loadComments() {
    const commentsContainer = document.getElementById('comments-container');

    if (commentsContainer) {
        // Get the current language
        const htmlLang = document.documentElement.lang || 'en';

        // Try to load comments from the appropriate JSON file based on language
        const commentsFile = htmlLang === 'ar' ? 'data/comments-ar.json' : 'data/comments-en.json';

        fetch(commentsFile)
            .then(response => {
                if (!response.ok) {
                    // If the new file format fails, try the old format as fallback
                    return fetch('comments.json')
                        .then(oldResponse => {
                            if (!oldResponse.ok) {
                                throw new Error('Failed to load comments');
                            }
                            return oldResponse.json();
                        });
                }
                return response.json();
            })
            .then(data => {
                // Clear existing comments
                commentsContainer.innerHTML = '';

                // Get comments based on the file format
                let allComments = [];

                // Check if we're using the new file format (direct comments array)
                if (Array.isArray(data.comments)) {
                    allComments = data.comments;
                }
                // Check if we're using the old file format (with language keys)
                else if (data.comments && typeof data.comments === 'object') {
                    if (data.comments[htmlLang]) {
                        // Use comments for current language
                        allComments = data.comments[htmlLang];
                    } else if (data.comments.en) {
                        // Fallback to English
                        allComments = data.comments.en;
                    } else if (data.comments.ar) {
                        // Fallback to Arabic
                        allComments = data.comments.ar;
                    } else if (Array.isArray(data.comments)) {
                        // Legacy support for old format
                        allComments = data.comments;
                    }
                }

                // Shuffle the comments to display them randomly
                const shuffledComments = shuffleArray([...allComments]);

                // Get the first 4 comments to display initially
                const initialComments = shuffledComments.slice(0, 4);

                // Add initial comments to the container
                initialComments.forEach(comment => {
                    // Convert the date string to a Date object
                    const commentData = {
                        ...comment,
                        date: new Date(comment.date)
                    };
                    addComment(commentData);
                });

                // Set up interval to rotate comments
                setInterval(() => {
                    // Remove the first comment
                    if (commentsContainer.children.length > 0) {
                        commentsContainer.removeChild(commentsContainer.children[0]);
                    }

                    // Get a random comment that's not currently displayed
                    const displayedCommentIds = Array.from(commentsContainer.querySelectorAll('.comment'))
                        .map(comment => comment.dataset.commentId);

                    const availableComments = shuffledComments.filter(comment =>
                        !displayedCommentIds.includes(comment.id.toString()));

                    if (availableComments.length > 0) {
                        // Get a random comment from available comments
                        const randomIndex = Math.floor(Math.random() * availableComments.length);
                        const newComment = availableComments[randomIndex];

                        // Add the new comment
                        const commentData = {
                            ...newComment,
                            date: new Date(newComment.date)
                        };

                        // Add to the end of the list with animation
                        addComment(commentData, true);
                    } else {
                        // If all comments have been displayed, reshuffle and start again
                        const reshuffledComments = shuffleArray([...allComments]);
                        const newComment = reshuffledComments[0];

                        const commentData = {
                            ...newComment,
                            date: new Date(newComment.date)
                        };

                        // Add to the end of the list with animation
                        addComment(commentData, true);
                    }
                }, 8000); // Rotate comments every 8 seconds
            })
            .catch(error => {
                console.error('Error loading comments:', error);

                // Get the current language
                const htmlLang = document.documentElement.lang || 'en';

                // Fallback to sample comments if JSON file can't be loaded
                let fallbackComments = [];

                if (htmlLang === 'ar') {
                    // Arabic fallback comments
                    fallbackComments = [
                        {
                            id: 1,
                            name: 'أحمد محمد',
                            date: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000), // 2 days ago
                            text: 'لم أصدق عندما تلقيت الاتصال بأنني ربحت! شكراً جزيلاً، الجهاز رائع ويعمل بشكل ممتاز.',
                            rating: 5,
                            avatar: 'https://randomuser.me/api/portraits/men/1.jpg',
                            verified: true,
                            location: 'الرياض، السعودية'
                        },
                        {
                            id: 2,
                            name: 'سارة أحمد',
                            date: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000), // 5 days ago
                            text: 'كنت متشككة في البداية، لكن المسابقة كانت حقيقية 100%! استلمت الجهاز خلال أسبوع من الإعلان عن فوزي.',
                            rating: 5,
                            avatar: 'https://randomuser.me/api/portraits/women/2.jpg',
                            verified: true,
                            location: 'دبي، الإمارات'
                        },
                        {
                            id: 3,
                            name: 'محمد علي',
                            date: new Date(Date.now() - 10 * 24 * 60 * 60 * 1000), // 10 days ago
                            text: 'تجربة رائعة! الموقع سهل الاستخدام والتسجيل كان بسيطاً. سأشارك مع أصدقائي للاشتراك أيضاً.',
                            rating: 4,
                            avatar: 'https://randomuser.me/api/portraits/men/3.jpg',
                            verified: false,
                            location: 'القاهرة، مصر'
                        },
                        {
                            id: 4,
                            name: 'فاطمة حسن',
                            date: new Date(Date.now() - 15 * 24 * 60 * 60 * 1000), // 15 days ago
                            text: 'شكراً جزيلاً! لم أتوقع أن أربح، لكنني كنت محظوظة. الجهاز يعمل بشكل ممتاز وأنا سعيدة جداً به.',
                            rating: 5,
                            avatar: 'https://randomuser.me/api/portraits/women/4.jpg',
                            verified: true,
                            location: 'الدوحة، قطر'
                        }
                    ];
                } else {
                    // English fallback comments
                    fallbackComments = [
                        {
                            id: 101,
                            name: 'John Smith',
                            date: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000), // 2 days ago
                            text: 'I couldn\'t believe it when I received the call that I won! Thank you so much, the device is amazing and works perfectly.',
                            rating: 5,
                            avatar: 'https://randomuser.me/api/portraits/men/21.jpg',
                            verified: true,
                            location: 'New York, USA'
                        },
                        {
                            id: 102,
                            name: 'Sarah Johnson',
                            date: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000), // 5 days ago
                            text: 'I was skeptical at first, but the contest was 100% real! I received the device within a week of being announced as the winner.',
                            rating: 5,
                            avatar: 'https://randomuser.me/api/portraits/women/22.jpg',
                            verified: true,
                            location: 'London, UK'
                        },
                        {
                            id: 103,
                            name: 'Michael Brown',
                            date: new Date(Date.now() - 10 * 24 * 60 * 60 * 1000), // 10 days ago
                            text: 'Great experience! The website is easy to use and registration was simple. I\'ll share with my friends to join too.',
                            rating: 4,
                            avatar: 'https://randomuser.me/api/portraits/men/23.jpg',
                            verified: false,
                            location: 'Sydney, Australia'
                        },
                        {
                            id: 104,
                            name: 'Emily Davis',
                            date: new Date(Date.now() - 15 * 24 * 60 * 60 * 1000), // 15 days ago
                            text: 'Thank you so much! I didn\'t expect to win, but I was lucky. The device works perfectly and I\'m very happy with it.',
                            rating: 5,
                            avatar: 'https://randomuser.me/api/portraits/women/24.jpg',
                            verified: true,
                            location: 'Toronto, Canada'
                        }
                    ];
                }

                // Add fallback comments to the container
                fallbackComments.forEach(comment => {
                    addComment(comment);
                });

                // Set up interval to rotate comments even with fallback data
                setInterval(() => {
                    // Remove the first comment
                    if (commentsContainer.children.length > 0) {
                        commentsContainer.removeChild(commentsContainer.children[0]);
                    }

                    // Add a random comment from the fallback list
                    const randomIndex = Math.floor(Math.random() * fallbackComments.length);
                    const randomComment = fallbackComments[randomIndex];

                    // Add to the end of the list with animation
                    addComment(randomComment, true);
                }, 8000); // Rotate comments every 8 seconds
            });
    }
}

/**
 * Shuffle array elements (Fisher-Yates algorithm)
 * @param {Array} array - The array to shuffle
 * @returns {Array} - The shuffled array
 */
function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

/**
 * Add a comment to the comments container
 * @param {Object} comment - Comment data
 * @param {boolean} animate - Whether to animate the comment entry
 */
function addComment(comment, animate = false) {
    const commentsContainer = document.getElementById('comments-container');

    const commentElement = document.createElement('div');
    commentElement.className = 'comment';

    // Add comment ID as data attribute for tracking
    if (comment.id) {
        commentElement.dataset.commentId = comment.id;
    }

    // Add animation class if requested
    if (animate) {
        commentElement.classList.add('comment-animated');
    }

    const commentHeader = document.createElement('div');
    commentHeader.className = 'comment-header';

    const commentAvatar = document.createElement('div');
    commentAvatar.className = 'comment-avatar';
    const avatarImg = document.createElement('img');
    avatarImg.src = comment.avatar || 'https://via.placeholder.com/50';
    avatarImg.alt = comment.name;
    commentAvatar.appendChild(avatarImg);

    const commentInfo = document.createElement('div');
    commentInfo.className = 'comment-info';
    const nameElement = document.createElement('h4');
    nameElement.textContent = comment.name;

    // Add verified badge if comment is verified
    if (comment.verified) {
        const verifiedBadge = document.createElement('span');
        verifiedBadge.className = 'verified-badge';
        verifiedBadge.innerHTML = '<i class="fas fa-check-circle"></i>';
        verifiedBadge.title = 'تم التحقق';
        nameElement.appendChild(verifiedBadge);
    }

    const dateElement = document.createElement('span');
    dateElement.className = 'comment-date';
    dateElement.textContent = formatDate(comment.date);

    // Add location if available
    if (comment.location) {
        const locationElement = document.createElement('span');
        locationElement.className = 'comment-location';
        locationElement.innerHTML = `<i class="fas fa-map-marker-alt"></i> ${comment.location}`;
        commentInfo.appendChild(nameElement);
        commentInfo.appendChild(dateElement);
        commentInfo.appendChild(locationElement);
    } else {
        commentInfo.appendChild(nameElement);
        commentInfo.appendChild(dateElement);
    }

    commentHeader.appendChild(commentAvatar);
    commentHeader.appendChild(commentInfo);

    const commentText = document.createElement('p');
    commentText.className = 'comment-text';
    commentText.textContent = comment.text;

    const commentRating = document.createElement('div');
    commentRating.className = 'comment-rating';
    commentRating.innerHTML = '★'.repeat(comment.rating) + '☆'.repeat(5 - comment.rating);

    commentElement.appendChild(commentHeader);
    commentElement.appendChild(commentText);
    commentElement.appendChild(commentRating);

    if (animate) {
        // Add to the end with animation
        commentsContainer.appendChild(commentElement);

        // Trigger animation by forcing a reflow and then adding the visible class
        void commentElement.offsetWidth;
        commentElement.classList.add('visible');
    } else {
        // Add normally
        commentsContainer.appendChild(commentElement);
    }
}

/**
 * Add a new comment (used for tracking CTA clicks)
 */
function addNewComment() {
    // Get the current language
    const htmlLang = document.documentElement.lang || 'ar';

    // Create a new comment with the current user's information
    const comment = {
        name: htmlLang === 'ar' ? "أنت" : "You",
        date: new Date(),
        text: document.querySelector('meta[name="new-comment-text"]')?.content ||
              (htmlLang === 'ar' ? 'لقد نقرت للتو على زر "احصل عليه الآن"! أتمنى الفوز بالجائزة.' :
                                  'I just clicked the "Get It Now" button! I hope to win the prize.'),
        rating: 5,
        avatar: `https://randomuser.me/api/portraits/${Math.random() > 0.5 ? 'men' : 'women'}/${Math.floor(Math.random() * 100)}.jpg`,
        verified: false,
        location: ""
    };

    // Add the comment to the top of the list
    const commentsContainer = document.getElementById('comments-container');

    if (commentsContainer) {
        const commentElement = document.createElement('div');
        commentElement.className = 'comment';
        commentElement.style.animation = 'fadeIn 0.5s ease-in-out';

        const commentHeader = document.createElement('div');
        commentHeader.className = 'comment-header';

        const commentAvatar = document.createElement('div');
        commentAvatar.className = 'comment-avatar';
        const avatarImg = document.createElement('img');
        avatarImg.src = comment.avatar;
        avatarImg.alt = comment.name;
        commentAvatar.appendChild(avatarImg);

        const commentInfo = document.createElement('div');
        commentInfo.className = 'comment-info';
        const nameElement = document.createElement('h4');
        nameElement.textContent = comment.name;
        const dateElement = document.createElement('span');
        dateElement.className = 'comment-date';
        dateElement.textContent = document.querySelector('meta[name="comments-now-text"]')?.content ||
                                 (htmlLang === 'ar' ? 'الآن' : 'Now');
        commentInfo.appendChild(nameElement);
        commentInfo.appendChild(dateElement);

        commentHeader.appendChild(commentAvatar);
        commentHeader.appendChild(commentInfo);

        const commentText = document.createElement('p');
        commentText.className = 'comment-text';
        commentText.textContent = comment.text;

        const commentRating = document.createElement('div');
        commentRating.className = 'comment-rating';
        commentRating.innerHTML = '★'.repeat(comment.rating) + '☆'.repeat(5 - comment.rating);

        commentElement.appendChild(commentHeader);
        commentElement.appendChild(commentText);
        commentElement.appendChild(commentRating);

        // Add to the top of the comments container
        commentsContainer.insertBefore(commentElement, commentsContainer.firstChild);
    }
}

/**
 * Format date to a readable string
 * @param {Date} date - Date to format
 * @returns {string} - Formatted date string
 */
function formatDate(date) {
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    // Get the current language
    const htmlLang = document.documentElement.lang || 'en';

    if (htmlLang === 'ar') {
        // Arabic date formatting
        if (diffDays === 0) {
            return 'اليوم';
        } else if (diffDays === 1) {
            return 'الأمس';
        } else if (diffDays < 7) {
            return `منذ ${diffDays} أيام`;
        } else if (diffDays < 30) {
            const weeks = Math.floor(diffDays / 7);
            return `منذ ${weeks} ${weeks === 1 ? 'أسبوع' : 'أسابيع'}`;
        } else {
            const months = Math.floor(diffDays / 30);
            return `منذ ${months} ${months === 1 ? 'شهر' : 'أشهر'}`;
        }
    } else {
        // English date formatting
        if (diffDays === 0) {
            return 'Today';
        } else if (diffDays === 1) {
            return 'Yesterday';
        } else if (diffDays < 7) {
            return `${diffDays} days ago`;
        } else if (diffDays < 30) {
            const weeks = Math.floor(diffDays / 7);
            return `${weeks} ${weeks === 1 ? 'week' : 'weeks'} ago`;
        } else {
            const months = Math.floor(diffDays / 30);
            return `${months} ${months === 1 ? 'month' : 'months'} ago`;
        }
    }
}
