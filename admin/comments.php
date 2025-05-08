<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Set page title
$pageTitle = 'إدارة التعليقات';

// Get comments from separate JSON files
$commentsArFile = '../data/comments-ar.json';
$commentsEnFile = '../data/comments-en.json';
$oldCommentsFile = '../comments.json';
$dataDir = '../data';

$comments = [
    'ar' => [],
    'en' => []
];

// Create data directory if it doesn't exist
if (!file_exists($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Load Arabic comments
if (file_exists($commentsArFile)) {
    $commentsJson = file_get_contents($commentsArFile);
    $commentsData = json_decode($commentsJson, true);

    if (isset($commentsData['comments']) && is_array($commentsData['comments'])) {
        $comments['ar'] = $commentsData['comments'];
    }
}

// Load English comments
if (file_exists($commentsEnFile)) {
    $commentsJson = file_get_contents($commentsEnFile);
    $commentsData = json_decode($commentsJson, true);

    if (isset($commentsData['comments']) && is_array($commentsData['comments'])) {
        $comments['en'] = $commentsData['comments'];
    }
}

// Fallback to old format if needed
$oldCommentsFile = '../data/comments.json';
if ((empty($comments['ar']) || empty($comments['en'])) && file_exists($oldCommentsFile)) {
    $commentsJson = file_get_contents($oldCommentsFile);
    $commentsData = json_decode($commentsJson, true);

    if (isset($commentsData['comments'])) {
        // Check if comments are in the old format (with language keys)
        if (isset($commentsData['comments']['ar']) && empty($comments['ar'])) {
            $comments['ar'] = $commentsData['comments']['ar'];
        }
        if (isset($commentsData['comments']['en']) && empty($comments['en'])) {
            $comments['en'] = $commentsData['comments']['en'];
        } else if (!isset($commentsData['comments']['ar']) && !isset($commentsData['comments']['en']) && empty($comments['ar'])) {
            // Legacy format - move all comments to Arabic
            $comments['ar'] = $commentsData['comments'];
        }
    }
}

// If comments still don't exist in data directory, check if they exist in root
if ((empty($comments['ar']) || empty($comments['en'])) && file_exists('../comments.json')) {
    $commentsJson = file_get_contents('../comments.json');
    $commentsData = json_decode($commentsJson, true);

    if (isset($commentsData['comments'])) {
        // Check if comments are in the old format (with language keys)
        if (isset($commentsData['comments']['ar']) && empty($comments['ar'])) {
            $comments['ar'] = $commentsData['comments']['ar'];
        }
        if (isset($commentsData['comments']['en']) && empty($comments['en'])) {
            $comments['en'] = $commentsData['comments']['en'];
        } else if (!isset($commentsData['comments']['ar']) && !isset($commentsData['comments']['en']) && empty($comments['ar'])) {
            // Legacy format - move all comments to Arabic
            $comments['ar'] = $commentsData['comments'];
        }
    }
}

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete comment
    if (isset($_POST['delete_comment'])) {
        $commentId = (int)$_POST['delete_comment'];
        $foundComment = false;

        // Find and remove the comment from all language arrays
        foreach ($comments as $lang => $langComments) {
            foreach ($langComments as $key => $comment) {
                if ($comment['id'] === $commentId) {
                    unset($comments[$lang][$key]);
                    // Reindex the array
                    $comments[$lang] = array_values($comments[$lang]);
                    $foundComment = true;
                    break 2;
                }
            }
        }

        if ($foundComment) {
            // Save comments to separate files
            $success = true;

            // Save Arabic comments
            $arData = ['comments' => $comments['ar']];
            $arJson = json_encode($arData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (!file_put_contents($commentsArFile, $arJson)) {
                $success = false;
            }

            // Save English comments
            $enData = ['comments' => $comments['en']];
            $enJson = json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (!file_put_contents($commentsEnFile, $enJson)) {
                $success = false;
            }

            if ($success) {
                $message = 'تم حذف التعليق بنجاح';
                $messageType = 'success';
            } else {
                $message = 'حدث خطأ أثناء حذف التعليق';
                $messageType = 'danger';
            }
        } else {
            $message = 'لم يتم العثور على التعليق';
            $messageType = 'danger';
        }
    }

    // Toggle comment verification
    if (isset($_POST['toggle_verify'])) {
        $commentId = (int)$_POST['toggle_verify'];
        $verified = (int)$_POST['verified'];
        $foundComment = false;

        // Find and update the comment in all language arrays
        foreach ($comments as $lang => $langComments) {
            foreach ($langComments as $key => $comment) {
                if ($comment['id'] === $commentId) {
                    $comments[$lang][$key]['verified'] = $verified === 1;
                    $foundComment = true;
                    break 2;
                }
            }
        }

        if ($foundComment) {
            // Save comments to separate files
            $success = true;

            // Save Arabic comments
            $arData = ['comments' => $comments['ar']];
            $arJson = json_encode($arData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (!file_put_contents($commentsArFile, $arJson)) {
                $success = false;
            }

            // Save English comments
            $enData = ['comments' => $comments['en']];
            $enJson = json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (!file_put_contents($commentsEnFile, $enJson)) {
                $success = false;
            }

            if ($success) {
                $message = 'تم تحديث حالة التحقق بنجاح';
                $messageType = 'success';
            } else {
                $message = 'حدث خطأ أثناء تحديث حالة التحقق';
                $messageType = 'danger';
            }
        } else {
            $message = 'لم يتم العثور على التعليق';
            $messageType = 'danger';
        }
    }

    // Edit comment
    if (isset($_POST['edit_comment'])) {
        $commentId = (int)$_POST['edit_comment'];

        // Get custom date and time or use current date and time
        $commentDate = !empty($_POST['edit_comment_date']) ? $_POST['edit_comment_date'] : date('Y-m-d');
        $commentTime = !empty($_POST['edit_comment_time']) ? $_POST['edit_comment_time'] : date('H:i');

        // Get comment language
        $commentLanguage = isset($_POST['edit_comment_language']) ? $_POST['edit_comment_language'] : 'ar';
        $oldLanguage = null;
        $foundComment = false;
        $commentKey = null;

        // Combine date and time into ISO format
        $dateTime = $commentDate . 'T' . $commentTime . ':00';

        // Find the comment in all language arrays
        foreach ($comments as $lang => $langComments) {
            foreach ($langComments as $key => $comment) {
                if ($comment['id'] === $commentId) {
                    $oldLanguage = $lang;
                    $commentKey = $key;
                    $foundComment = true;
                    break 2;
                }
            }
        }

        if ($foundComment) {
            // Create updated comment
            $updatedComment = [
                'id' => $commentId,
                'name' => $_POST['edit_name'],
                'date' => $dateTime,
                'text' => $_POST['edit_text'],
                'rating' => (int)$_POST['edit_rating'],
                'avatar' => $_POST['edit_avatar'],
                'verified' => isset($_POST['edit_verified']) && $_POST['edit_verified'] === '1',
                'location' => $_POST['edit_location']
            ];

            // If language changed, remove from old language array and add to new one
            if ($oldLanguage !== $commentLanguage) {
                // Remove from old language array
                unset($comments[$oldLanguage][$commentKey]);
                // Reindex the array
                $comments[$oldLanguage] = array_values($comments[$oldLanguage]);
                // Add to new language array
                $comments[$commentLanguage][] = $updatedComment;
            } else {
                // Update in the same language array
                $comments[$oldLanguage][$commentKey] = $updatedComment;
            }

            // Save comments to separate files
            $success = true;

            // Save Arabic comments
            $arData = ['comments' => $comments['ar']];
            $arJson = json_encode($arData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (!file_put_contents($commentsArFile, $arJson)) {
                $success = false;
            }

            // Save English comments
            $enData = ['comments' => $comments['en']];
            $enJson = json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (!file_put_contents($commentsEnFile, $enJson)) {
                $success = false;
            }

            if ($success) {
                $message = 'تم تعديل التعليق بنجاح';
                $messageType = 'success';
            } else {
                $message = 'حدث خطأ أثناء تعديل التعليق';
                $messageType = 'danger';
            }
        } else {
            $message = 'لم يتم العثور على التعليق';
            $messageType = 'danger';
        }
    }

    // Add new comment
    if (isset($_POST['add_comment'])) {
        // Get custom date and time or use current date and time
        $commentDate = !empty($_POST['comment_date']) ? $_POST['comment_date'] : date('Y-m-d');
        $commentTime = !empty($_POST['comment_time']) ? $_POST['comment_time'] : date('H:i');

        // Get comment language
        $commentLanguage = isset($_POST['comment_language']) ? $_POST['comment_language'] : 'ar';

        // Combine date and time into ISO format
        $dateTime = $commentDate . 'T' . $commentTime . ':00';

        // Generate a unique ID for the comment
        $commentId = time();

        // If it's an English comment, add 100 to the ID to avoid conflicts
        if ($commentLanguage === 'en') {
            $commentId += 100;
        }

        $newComment = [
            'id' => $commentId,
            'name' => $_POST['name'],
            'date' => $dateTime,
            'text' => $_POST['text'],
            'rating' => (int)$_POST['rating'],
            'avatar' => $_POST['avatar'],
            'verified' => isset($_POST['verified']) && $_POST['verified'] === '1',
            'location' => $_POST['location']
        ];

        // Add the new comment to the appropriate language array
        $comments[$commentLanguage][] = $newComment;

        // Save comments to separate files
        $success = true;

        // Save Arabic comments
        $arData = ['comments' => $comments['ar']];
        $arJson = json_encode($arData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (!file_put_contents($commentsArFile, $arJson)) {
            $success = false;
        }

        // Save English comments
        $enData = ['comments' => $comments['en']];
        $enJson = json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (!file_put_contents($commentsEnFile, $enJson)) {
            $success = false;
        }

        if ($success) {
            $message = 'تم إضافة التعليق بنجاح';
            $messageType = 'success';
        } else {
            $message = 'حدث خطأ أثناء إضافة التعليق';
            $messageType = 'danger';
        }
    }
}

// Include header
include 'includes/header.php';
?>

<div class="admin-content">
    <div class="admin-content-inner">
        <div class="admin-page-header">
            <h1>إدارة التعليقات</h1>
            <p>إدارة تعليقات موقع المسابقة</p>
        </div>

        <?php if ($message): ?>
        <div class="admin-alert admin-alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- عرض التعليقات بطريقة مبسطة -->
        <div class="admin-simple-tabs">
            <div class="admin-simple-tabs-header">
                <button class="admin-simple-tab-button active" onclick="showTab('simple-ar-comments')">التعليقات العربية (<?php echo count($comments['ar']); ?>)</button>
                <button class="admin-simple-tab-button" onclick="showTab('simple-en-comments')">التعليقات الإنجليزية (<?php echo count($comments['en']); ?>)</button>
                <button class="admin-simple-tab-button" onclick="showTab('simple-add-comment')">إضافة تعليق جديد</button>
            </div>

            <div class="admin-simple-tabs-content">
                <!-- التعليقات العربية -->
                <div class="admin-simple-tab-pane active" id="simple-ar-comments">
                    <h3>التعليقات العربية</h3>

                    <?php if (empty($comments['ar'])): ?>
                    <p class="admin-empty-message">لا توجد تعليقات عربية</p>
                    <?php else: ?>
                        <div class="admin-comments-grid">
                            <?php foreach ($comments['ar'] as $comment): ?>
                            <div class="admin-comment-card">
                                <div class="admin-comment-card-header">
                                    <img src="<?php echo $comment['avatar']; ?>" alt="<?php echo $comment['name']; ?>" class="admin-comment-card-avatar">
                                    <div class="admin-comment-card-info">
                                        <h4><?php echo $comment['name']; ?>
                                            <?php if (isset($comment['verified']) && $comment['verified']): ?>
                                            <span class="admin-badge admin-badge-success">تم التحقق</span>
                                            <?php endif; ?>
                                        </h4>
                                        <span><?php echo date('Y-m-d', strtotime($comment['date'])); ?></span>
                                        <?php if (isset($comment['location']) && $comment['location']): ?>
                                        <span><i class="fas fa-map-marker-alt"></i> <?php echo $comment['location']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="admin-comment-card-body">
                                    <p><?php echo $comment['text']; ?></p>
                                    <div class="admin-comment-card-rating">
                                        <?php echo str_repeat('★', $comment['rating']) . str_repeat('☆', 5 - $comment['rating']); ?>
                                    </div>
                                </div>
                                <div class="admin-comment-card-actions">
                                    <form method="post" action="comments.php" style="display: inline-block;">
                                        <input type="hidden" name="toggle_verify" value="<?php echo $comment['id']; ?>">
                                        <input type="hidden" name="verified" value="<?php echo isset($comment['verified']) && $comment['verified'] ? '0' : '1'; ?>">
                                        <button type="submit" class="admin-button admin-button-sm">
                                            <?php if (isset($comment['verified']) && $comment['verified']): ?>
                                            <i class="fas fa-times-circle"></i> إلغاء التحقق
                                            <?php else: ?>
                                            <i class="fas fa-check-circle"></i> تحقق
                                            <?php endif; ?>
                                        </button>
                                    </form>

                                    <button class="admin-button admin-button-sm admin-button-info" onclick="editComment(<?php echo $comment['id']; ?>, 'ar')">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>

                                    <form method="post" action="comments.php" style="display: inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذا التعليق؟');">
                                        <input type="hidden" name="delete_comment" value="<?php echo $comment['id']; ?>">
                                        <button type="submit" class="admin-button admin-button-sm admin-button-danger">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- التعليقات الإنجليزية -->
                <div class="admin-simple-tab-pane" id="simple-en-comments">
                    <h3>التعليقات الإنجليزية</h3>

                    <?php if (empty($comments['en'])): ?>
                    <p class="admin-empty-message">لا توجد تعليقات إنجليزية</p>
                    <?php else: ?>
                        <div class="admin-comments-grid">
                            <?php foreach ($comments['en'] as $comment): ?>
                            <div class="admin-comment-card">
                                <div class="admin-comment-card-header">
                                    <img src="<?php echo $comment['avatar']; ?>" alt="<?php echo $comment['name']; ?>" class="admin-comment-card-avatar">
                                    <div class="admin-comment-card-info">
                                        <h4><?php echo $comment['name']; ?>
                                            <?php if (isset($comment['verified']) && $comment['verified']): ?>
                                            <span class="admin-badge admin-badge-success">تم التحقق</span>
                                            <?php endif; ?>
                                        </h4>
                                        <span><?php echo date('Y-m-d', strtotime($comment['date'])); ?></span>
                                        <?php if (isset($comment['location']) && $comment['location']): ?>
                                        <span><i class="fas fa-map-marker-alt"></i> <?php echo $comment['location']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="admin-comment-card-body">
                                    <p><?php echo $comment['text']; ?></p>
                                    <div class="admin-comment-card-rating">
                                        <?php echo str_repeat('★', $comment['rating']) . str_repeat('☆', 5 - $comment['rating']); ?>
                                    </div>
                                </div>
                                <div class="admin-comment-card-actions">
                                    <form method="post" action="comments.php" style="display: inline-block;">
                                        <input type="hidden" name="toggle_verify" value="<?php echo $comment['id']; ?>">
                                        <input type="hidden" name="verified" value="<?php echo isset($comment['verified']) && $comment['verified'] ? '0' : '1'; ?>">
                                        <button type="submit" class="admin-button admin-button-sm">
                                            <?php if (isset($comment['verified']) && $comment['verified']): ?>
                                            <i class="fas fa-times-circle"></i> إلغاء التحقق
                                            <?php else: ?>
                                            <i class="fas fa-check-circle"></i> تحقق
                                            <?php endif; ?>
                                        </button>
                                    </form>

                                    <button class="admin-button admin-button-sm admin-button-info" onclick="editComment(<?php echo $comment['id']; ?>, 'en')">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>

                                    <form method="post" action="comments.php" style="display: inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذا التعليق؟');">
                                        <input type="hidden" name="delete_comment" value="<?php echo $comment['id']; ?>">
                                        <button type="submit" class="admin-button admin-button-sm admin-button-danger">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- إضافة تعليق جديد -->
                <div class="admin-simple-tab-pane" id="simple-add-comment">
                    <h3>إضافة تعليق جديد</h3>

                    <form method="post" action="comments.php" class="admin-form">
                        <input type="hidden" name="add_comment" value="1">

                        <div class="admin-form-row">
                            <div class="admin-form-col">
                                <label for="name">الاسم</label>
                                <input type="text" id="name" name="name" required>
                            </div>

                            <div class="admin-form-col">
                                <label for="location">الموقع</label>
                                <input type="text" id="location" name="location" placeholder="مثال: الرياض، السعودية">
                            </div>
                        </div>

                        <div class="admin-form-row">
                            <div class="admin-form-col">
                                <label for="comment-date">تاريخ التعليق</label>
                                <input type="date" id="comment-date" name="comment_date" value="<?php echo date('Y-m-d'); ?>">
                                <p class="admin-form-help">حدد تاريخ نشر التعليق</p>
                            </div>

                            <div class="admin-form-col">
                                <label for="comment-time">وقت التعليق</label>
                                <input type="time" id="comment-time" name="comment_time" value="<?php echo date('H:i'); ?>">
                                <p class="admin-form-help">حدد وقت نشر التعليق</p>
                            </div>
                        </div>

                        <div class="admin-form-row">
                            <div class="admin-form-col">
                                <label for="comment-language">لغة التعليق</label>
                                <select id="comment-language" name="comment_language">
                                    <option value="ar">العربية</option>
                                    <option value="en">الإنجليزية</option>
                                </select>
                                <p class="admin-form-help">اختر اللغة التي سيظهر بها التعليق</p>
                            </div>
                        </div>

                        <div class="admin-form-row">
                            <div class="admin-form-col">
                                <label for="avatar">رابط الصورة الرمزية</label>
                                <div class="admin-avatar-input-group">
                                    <input type="text" id="avatar" name="avatar" value="https://randomuser.me/api/portraits/men/<?php echo rand(1, 99); ?>.jpg">
                                    <div class="admin-avatar-preview" id="avatar-preview"></div>
                                </div>
                                <p class="admin-form-help">يمكنك استخدام رابط صورة من randomuser.me أو أي مصدر آخر</p>
                            </div>

                            <div class="admin-form-col">
                                <label for="rating">التقييم</label>
                                <select id="rating" name="rating" required>
                                    <option value="5">5 نجوم</option>
                                    <option value="4">4 نجوم</option>
                                    <option value="3">3 نجوم</option>
                                    <option value="2">2 نجوم</option>
                                    <option value="1">1 نجمة</option>
                                </select>
                            </div>
                        </div>

                        <div class="admin-form-row">
                            <div class="admin-form-col">
                                <label for="text">التعليق</label>
                                <textarea id="text" name="text" rows="5" required></textarea>
                            </div>
                        </div>

                        <div class="admin-form-row">
                            <div class="admin-form-col">
                                <div class="admin-checkbox">
                                    <input type="checkbox" id="verified" name="verified" value="1" checked>
                                    <label for="verified">تم التحقق</label>
                                </div>
                            </div>
                        </div>

                        <div class="admin-form-actions">
                            <button type="submit" class="admin-button admin-button-primary">إضافة تعليق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* أنماط مبسطة لعرض التعليقات */
.admin-simple-tabs {
    margin-bottom: 30px;
}

.admin-simple-tabs-header {
    display: flex;
    border-bottom: 2px solid #ddd;
    margin-bottom: 20px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: #ddd transparent;
}

.admin-simple-tabs-header::-webkit-scrollbar {
    height: 4px;
}

.admin-simple-tabs-header::-webkit-scrollbar-track {
    background: transparent;
}

.admin-simple-tabs-header::-webkit-scrollbar-thumb {
    background-color: #ddd;
    border-radius: 4px;
}

.admin-simple-tab-button {
    padding: 12px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-weight: bold;
    color: #666;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    white-space: nowrap;
    min-width: max-content;
    transition: all 0.3s ease;
}

.admin-simple-tab-button.active {
    color: #0071e3;
    border-bottom-color: #0071e3;
}

.admin-simple-tab-pane {
    display: none;
}

.admin-simple-tab-pane.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.admin-comments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.admin-comment-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 15px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.admin-comment-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.admin-comment-card-header {
    display: flex;
    margin-bottom: 10px;
}

.admin-comment-card-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    margin-left: 10px;
    flex-shrink: 0;
}

.admin-comment-card-info {
    flex: 1;
    min-width: 0; /* Ensures text wrapping works properly */
}

.admin-comment-card-info h4 {
    margin: 0 0 5px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.admin-comment-card-body {
    margin-bottom: 15px;
}

.admin-comment-card-body p {
    word-break: break-word;
    margin: 0;
}

.admin-comment-card-rating {
    color: #f8c01a;
    margin-top: 5px;
}

.admin-comment-card-actions {
    display: flex;
    justify-content: space-between;
    border-top: 1px solid #eee;
    padding-top: 10px;
    flex-wrap: wrap;
    gap: 5px;
}

.admin-empty-message {
    text-align: center;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    color: #666;
}

/* Responsive styles for comments management */
@media (max-width: 992px) {
    .admin-comments-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}

@media (max-width: 768px) {
    .admin-comments-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }

    .admin-comment-card-actions {
        flex-direction: column;
        gap: 8px;
    }

    .admin-comment-card-actions form {
        width: 100%;
    }

    .admin-comment-card-actions button {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .admin-comments-grid {
        grid-template-columns: 1fr;
    }

    .admin-simple-tab-button {
        padding: 10px 15px;
        font-size: 0.9rem;
    }

    .admin-comment-card {
        padding: 12px;
    }
}
</style>



<script>
function showTab(tabId) {
    // إخفاء جميع علامات التبويب
    document.querySelectorAll('.admin-simple-tab-pane').forEach(pane => {
        pane.classList.remove('active');
    });

    // إزالة الفئة النشطة من جميع الأزرار
    document.querySelectorAll('.admin-simple-tab-button').forEach(button => {
        button.classList.remove('active');
    });

    // إظهار علامة التبويب المحددة
    document.getElementById(tabId).classList.add('active');

    // تحديد الزر النشط
    const buttonIndex = tabId === 'simple-ar-comments' ? 0 : (tabId === 'simple-en-comments' ? 1 : 2);
    document.querySelectorAll('.admin-simple-tab-button')[buttonIndex].classList.add('active');
}

function editComment(commentId, language) {
    // البحث عن التعليق في مصفوفة التعليقات
    const comments = <?php echo json_encode($comments); ?>;
    let comment = null;

    // البحث عن التعليق بالمعرف
    for (let i = 0; i < comments[language].length; i++) {
        if (comments[language][i].id === commentId) {
            comment = comments[language][i];
            break;
        }
    }

    if (comment) {
        // التأكد من وجود النافذة المنبثقة
        let modal = document.getElementById('editCommentModal');

        // إذا لم تكن النافذة المنبثقة موجودة، قم بإنشائها
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'editCommentModal';
            modal.className = 'admin-modal';

            const modalContent = `
                <div class="admin-modal-content">
                    <div class="admin-modal-header">
                        <h3>تعديل التعليق</h3>
                        <span class="admin-modal-close">&times;</span>
                    </div>
                    <div class="admin-modal-body">
                        <form id="edit-comment-form" method="post" action="comments.php" class="admin-form">
                            <input type="hidden" name="edit_comment" id="edit-comment-id" value="">
                            <input type="hidden" name="edit_comment_language" id="edit-comment-language" value="">

                            <div class="admin-form-row">
                                <div class="admin-form-col">
                                    <label for="edit-name">الاسم</label>
                                    <input type="text" id="edit-name" name="edit_name" required>
                                </div>

                                <div class="admin-form-col">
                                    <label for="edit-location">الموقع</label>
                                    <input type="text" id="edit-location" name="edit_location">
                                </div>
                            </div>

                            <div class="admin-form-row">
                                <div class="admin-form-col">
                                    <label for="edit-comment-date">تاريخ التعليق</label>
                                    <input type="date" id="edit-comment-date" name="edit_comment_date">
                                </div>

                                <div class="admin-form-col">
                                    <label for="edit-comment-time">وقت التعليق</label>
                                    <input type="time" id="edit-comment-time" name="edit_comment_time">
                                </div>
                            </div>

                            <div class="admin-form-row">
                                <div class="admin-form-col">
                                    <label for="edit-avatar">رابط الصورة الرمزية</label>
                                    <div class="admin-avatar-input-group">
                                        <input type="text" id="edit-avatar" name="edit_avatar">
                                        <div class="admin-avatar-preview" id="edit-avatar-preview"></div>
                                    </div>
                                </div>

                                <div class="admin-form-col">
                                    <label for="edit-rating">التقييم</label>
                                    <select id="edit-rating" name="edit_rating" required>
                                        <option value="5">5 نجوم</option>
                                        <option value="4">4 نجوم</option>
                                        <option value="3">3 نجوم</option>
                                        <option value="2">2 نجوم</option>
                                        <option value="1">1 نجمة</option>
                                    </select>
                                </div>
                            </div>

                            <div class="admin-form-row">
                                <div class="admin-form-col">
                                    <label for="edit-text">التعليق</label>
                                    <textarea id="edit-text" name="edit_text" rows="5" required></textarea>
                                </div>
                            </div>

                            <div class="admin-form-row">
                                <div class="admin-form-col">
                                    <div class="admin-checkbox">
                                        <input type="checkbox" id="edit-verified" name="edit_verified" value="1">
                                        <label for="edit-verified">تم التحقق</label>
                                    </div>
                                </div>
                            </div>

                            <div class="admin-form-actions">
                                <button type="submit" class="admin-button admin-button-primary">حفظ التغييرات</button>
                                <button type="button" class="admin-button admin-modal-cancel">إلغاء</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;

            // Add additional styles for the modal form
            const additionalStyles = document.createElement('style');
            additionalStyles.textContent = `
                .admin-avatar-input-group {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                .admin-avatar-preview {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    background-size: cover;
                    background-position: center;
                    border: 1px solid #ddd;
                    flex-shrink: 0;
                }

                @media (max-width: 768px) {
                    .admin-form-row {
                        margin: 0 -10px 15px;
                    }

                    .admin-form-col {
                        padding: 0 10px;
                        margin-bottom: 15px;
                    }

                    .admin-form-actions {
                        display: flex;
                        flex-direction: column;
                        gap: 10px;
                    }

                    .admin-form-actions .admin-button {
                        width: 100%;
                        margin: 0;
                    }
                }

                @media (max-width: 576px) {
                    .admin-form-row {
                        flex-direction: column;
                        margin: 0 0 10px;
                    }

                    .admin-form-col {
                        padding: 0;
                        margin-bottom: 15px;
                        width: 100%;
                    }
                }
            `;
            document.head.appendChild(additionalStyles);

            modal.innerHTML = modalContent;
            document.body.appendChild(modal);

            // إضافة أنماط CSS للنافذة المنبثقة
            const style = document.createElement('style');
            style.textContent = `
                .admin-modal {
                    display: none;
                    position: fixed;
                    z-index: 1000;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    overflow: auto;
                    background-color: rgba(0, 0, 0, 0.5);
                }

                .admin-modal-content {
                    background-color: #fff;
                    margin: 5% auto;
                    padding: 20px;
                    border-radius: 8px;
                    width: 90%;
                    max-width: 800px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
                    animation: modalFadeIn 0.3s;
                }

                @keyframes modalFadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(-30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .admin-modal-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 15px;
                    margin-bottom: 20px;
                }

                .admin-modal-header h3 {
                    margin: 0;
                    font-size: 1.3rem;
                }

                .admin-modal-close {
                    font-size: 24px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: color 0.2s ease;
                }

                .admin-modal-close:hover {
                    color: #0071e3;
                }

                .admin-modal-body {
                    max-height: 70vh;
                    overflow-y: auto;
                    padding-right: 5px;
                }

                .admin-modal-body::-webkit-scrollbar {
                    width: 5px;
                }

                .admin-modal-body::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 5px;
                }

                .admin-modal-body::-webkit-scrollbar-thumb {
                    background: #ddd;
                    border-radius: 5px;
                }

                .admin-modal-body::-webkit-scrollbar-thumb:hover {
                    background: #ccc;
                }

                /* Responsive modal styles */
                @media (max-width: 768px) {
                    .admin-modal-content {
                        width: 95%;
                        margin: 10% auto;
                        padding: 15px;
                    }

                    .admin-modal-header h3 {
                        font-size: 1.2rem;
                    }
                }

                @media (max-width: 576px) {
                    .admin-modal-content {
                        width: 98%;
                        margin: 5% auto;
                        padding: 12px;
                    }

                    .admin-modal-header {
                        padding-bottom: 12px;
                        margin-bottom: 15px;
                    }

                    .admin-modal-body {
                        max-height: 80vh;
                    }
                }
            `;
            document.head.appendChild(style);

            // إضافة مستمعي الأحداث للنافذة المنبثقة
            const closeBtn = modal.querySelector('.admin-modal-close');
            const cancelBtn = modal.querySelector('.admin-modal-cancel');

            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            cancelBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // استخراج التاريخ والوقت
        let commentDate = '';
        let commentTime = '';

        if (comment.date) {
            try {
                const dateObj = new Date(comment.date);
                commentDate = dateObj.toISOString().split('T')[0];
                commentTime = dateObj.toTimeString().split(' ')[0].substring(0, 5);
            } catch (e) {
                console.error('Error parsing date:', e);
            }
        }

        // ملء النموذج
        document.getElementById('edit-comment-id').value = comment.id;
        document.getElementById('edit-name').value = comment.name;
        document.getElementById('edit-location').value = comment.location || '';
        document.getElementById('edit-comment-date').value = commentDate;
        document.getElementById('edit-comment-time').value = commentTime;
        document.getElementById('edit-comment-language').value = language;
        document.getElementById('edit-avatar').value = comment.avatar;

        // عرض صورة الأفاتار في المعاينة
        const avatarPreview = document.getElementById('edit-avatar-preview');
        if (avatarPreview) {
            avatarPreview.style.backgroundImage = `url('${comment.avatar}')`;
        }

        // إضافة مستمع حدث لتحديث معاينة الأفاتار عند تغيير الرابط
        const avatarInput = document.getElementById('edit-avatar');
        if (avatarInput) {
            avatarInput.addEventListener('input', function() {
                avatarPreview.style.backgroundImage = `url('${this.value}')`;
            });
        }

        document.getElementById('edit-rating').value = comment.rating;
        document.getElementById('edit-text').value = comment.text;
        document.getElementById('edit-verified').checked = comment.verified;

        // عرض النافذة المنبثقة
        modal.style.display = 'block';
    }
}

// تنفيذ الكود عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    console.log('تم تحميل صفحة التعليقات المبسطة');

    // تهيئة معاينة الأفاتار في نموذج إضافة التعليق
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');

    if (avatarInput && avatarPreview) {
        // عرض الصورة الأولية
        avatarPreview.style.backgroundImage = `url('${avatarInput.value}')`;

        // تحديث المعاينة عند تغيير الرابط
        avatarInput.addEventListener('input', function() {
            avatarPreview.style.backgroundImage = `url('${this.value}')`;
        });
    }

    // تحسين تجربة المستخدم على الأجهزة المحمولة
    const makeResponsive = function() {
        // تعديل عرض الشاشة للأجهزة المحمولة
        if (window.innerWidth <= 768) {
            // تعديلات خاصة بالأجهزة المحمولة
            document.querySelectorAll('.admin-comment-card-actions form').forEach(form => {
                form.style.width = '100%';
            });

            document.querySelectorAll('.admin-comment-card-actions button').forEach(button => {
                button.style.width = '100%';
                button.style.marginBottom = '5px';
            });
        } else {
            // إعادة التعيين للشاشات الكبيرة
            document.querySelectorAll('.admin-comment-card-actions form').forEach(form => {
                form.style.width = 'auto';
            });

            document.querySelectorAll('.admin-comment-card-actions button').forEach(button => {
                button.style.width = 'auto';
                button.style.marginBottom = '0';
            });
        }
    };

    // تنفيذ التعديلات عند تحميل الصفحة وعند تغيير حجم النافذة
    makeResponsive();
    window.addEventListener('resize', makeResponsive);
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
