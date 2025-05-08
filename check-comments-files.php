<?php
// أنشئ هذا الملف في المجلد الجذر للتحقق من ملفات التعليقات
header('Content-Type: text/plain');

echo "فحص ملفات التعليقات\n";
echo "===================\n\n";

// التحقق من وجود مجلد البيانات
$dataDir = 'data';
echo "مجلد البيانات: " . (file_exists($dataDir) ? "موجود" : "غير موجود") . "\n";

if (!file_exists($dataDir)) {
    echo "إنشاء مجلد البيانات... ";
    if (mkdir($dataDir, 0755, true)) {
        echo "تم بنجاح\n";
    } else {
        echo "فشل\n";
    }
}

// التحقق من ملفات التعليقات
$commentsArFile = 'data/comments-ar.json';
$commentsEnFile = 'data/comments-en.json';
$oldCommentsFile = 'comments.json';

echo "\nملف التعليقات العربية: " . (file_exists($commentsArFile) ? "موجود" : "غير موجود") . "\n";
if (file_exists($commentsArFile)) {
    echo "حجم الملف: " . filesize($commentsArFile) . " بايت\n";
    echo "صلاحيات الملف: " . substr(sprintf('%o', fileperms($commentsArFile)), -4) . "\n";
    
    $content = file_get_contents($commentsArFile);
    $data = json_decode($content, true);
    echo "هل يمكن تحليل JSON: " . ($data !== null ? "نعم" : "لا") . "\n";
    
    if ($data !== null) {
        echo "عدد التعليقات: " . (isset($data['comments']) ? count($data['comments']) : 0) . "\n";
    }
}

echo "\nملف التعليقات الإنجليزية: " . (file_exists($commentsEnFile) ? "موجود" : "غير موجود") . "\n";
if (file_exists($commentsEnFile)) {
    echo "حجم الملف: " . filesize($commentsEnFile) . " بايت\n";
    echo "صلاحيات الملف: " . substr(sprintf('%o', fileperms($commentsEnFile)), -4) . "\n";
    
    $content = file_get_contents($commentsEnFile);
    $data = json_decode($content, true);
    echo "هل يمكن تحليل JSON: " . ($data !== null ? "نعم" : "لا") . "\n";
    
    if ($data !== null) {
        echo "عدد التعليقات: " . (isset($data['comments']) ? count($data['comments']) : 0) . "\n";
    }
}

echo "\nملف التعليقات القديم: " . (file_exists($oldCommentsFile) ? "موجود" : "غير موجود") . "\n";
if (file_exists($oldCommentsFile)) {
    echo "حجم الملف: " . filesize($oldCommentsFile) . " بايت\n";
    echo "صلاحيات الملف: " . substr(sprintf('%o', fileperms($oldCommentsFile)), -4) . "\n";
    
    $content = file_get_contents($oldCommentsFile);
    $data = json_decode($content, true);
    echo "هل يمكن تحليل JSON: " . ($data !== null ? "نعم" : "لا") . "\n";
    
    if ($data !== null) {
        echo "عدد التعليقات العربية: " . (isset($data['comments']['ar']) ? count($data['comments']['ar']) : 0) . "\n";
        echo "عدد التعليقات الإنجليزية: " . (isset($data['comments']['en']) ? count($data['comments']['en']) : 0) . "\n";
    }
}

// إنشاء ملفات التعليقات إذا كانت غير موجودة
if (!file_exists($commentsArFile) && !file_exists($commentsEnFile) && file_exists($oldCommentsFile)) {
    echo "\nإنشاء ملفات التعليقات الجديدة من الملف القديم...\n";
    
    $content = file_get_contents($oldCommentsFile);
    $data = json_decode($content, true);
    
    if ($data !== null) {
        // إنشاء ملف التعليقات العربية
        if (isset($data['comments']['ar'])) {
            $arData = ['comments' => $data['comments']['ar']];
            $arJson = json_encode($arData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            echo "كتابة ملف التعليقات العربية... ";
            if (file_put_contents($commentsArFile, $arJson)) {
                echo "تم بنجاح\n";
            } else {
                echo "فشل\n";
            }
        }
        
        // إنشاء ملف التعليقات الإنجليزية
        if (isset($data['comments']['en'])) {
            $enData = ['comments' => $data['comments']['en']];
            $enJson = json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            echo "كتابة ملف التعليقات الإنجليزية... ";
            if (file_put_contents($commentsEnFile, $enJson)) {
                echo "تم بنجاح\n";
            } else {
                echo "فشل\n";
            }
        }
    }
}

echo "\nاكتمل الفحص.";
?>