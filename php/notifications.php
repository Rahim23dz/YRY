<?php
session_start();
include 'db.php'; // تأكد أن ملف الاتصال صحيح والمسار مضبوط

// فحص الجلسة وصلاحية المستخدم
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../html/login.php");
    exit;
}

$id_user = $_SESSION['user_id'];

// التأكد من الاتصال بقاعدة البيانات
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// جلب الإشعارات للمستخدم الحالي (دون id_commande)
$sql = "SELECT id, message, lu, date_creation FROM notifications WHERE id_user = ? ORDER BY date_creation DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("خطأ في تحضير الاستعلام: " . $conn->error);
}
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

// حساب الإحصائيات
$total_notifications = $result->num_rows;
$unread_count = 0;
$notifications = [];

while ($notif = $result->fetch_assoc()) {
    $notifications[] = $notif;
    if ($notif['lu'] == 0) {
        $unread_count++;
    }
}
$read_count = $total_notifications - $unread_count;
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📬 إشعاراتي</title>
    <style>
        /* ===== CSS MODERNE POUR NOTIFICATIONS ===== */
        :root {
            --primary: #00796b;
            --secondary: #004d40;
            --accent: #ff6f00;
            --success: #4caf50;
            --warning: #ff9800;
            --danger: #f44336;
            --info: #2196f3;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --border: #e0e0e0;
            --text: #212121;
            --text-secondary: #666666;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.15);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
            direction: rtl;
        }

        /* ===== HEADER MODERNE ===== */
        .page-header {
            background: linear-gradient(135deg, var(--white), var(--light-gray));
            padding: 25px 40px;
            box-shadow: var(--shadow);
            border-bottom: 4px solid var(--primary);
            margin-bottom: 30px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 30%, rgba(0, 121, 107, 0.1) 50%, transparent 70%);
            transform: translateX(-100%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .header-content {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
            z-index: 2;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-title::before {
            content: "📬";
            font-size: 2.5rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .header-title h1 {
            font-size: 2.2rem;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            margin: 0;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--primary), #4db6ac);
            color: var(--white);
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(0, 121, 107, 0.3);
            position: relative;
            overflow: hidden;
        }

        .back-link::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: var(--transition);
        }

        .back-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 121, 107, 0.4);
            background: linear-gradient(135deg, var(--secondary), var(--primary));
        }

        .back-link:hover::before {
            left: 100%;
        }

        /* ===== STATISTIQUES ===== */
        .stats-section {
            max-width: 1000px;
            margin: 0 auto 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border-left: 4px solid var(--primary);
        }

        .stat-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 0%, rgba(0, 121, 107, 0.05) 100%);
            opacity: 0;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:nth-child(1) { border-left-color: var(--info); animation-delay: 0.1s; }
        .stat-card:nth-child(2) { border-left-color: var(--warning); animation-delay: 0.2s; }
        .stat-card:nth-child(3) { border-left-color: var(--success); animation-delay: 0.3s; }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: block;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* ===== BARRE D'ACTIONS ===== */
        .actions-section {
            max-width: 1000px;
            margin: 0 auto 30px;
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-container {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .search-input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid var(--border);
            border-radius: 25px;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--light-gray);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(0, 121, 107, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.2rem;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border: 2px solid var(--border);
            background: var(--white);
            color: var(--text);
            border-radius: 20px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .filter-btn .count {
            background: var(--accent);
            color: var(--white);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
            margin-right: 5px;
        }

        /* ===== CONTAINER PRINCIPAL ===== */
        .notif-container {
            max-width: 1000px;
            margin: 0 auto;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .notifications-header {
            background: linear-gradient(135deg, var(--primary), #4db6ac);
            color: var(--white);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notifications-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }

        .notifications-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.9rem;
        }

        /* ===== LISTE DES NOTIFICATIONS ===== */
        .notifications-list {
            padding: 20px;
            max-height: 600px;
            overflow-y: auto;
        }

        .notifications-list::-webkit-scrollbar {
            width: 8px;
        }

        .notifications-list::-webkit-scrollbar-track {
            background: var(--light-gray);
            border-radius: 4px;
        }

        .notifications-list::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        .notif {
            background: var(--light-gray);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border-right: 4px solid var(--border);
        }

        .notif::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 0%, rgba(0, 121, 107, 0.05) 100%);
            opacity: 0;
            transition: var(--transition);
        }

        .notif:hover {
            transform: translateX(-5px);
            box-shadow: var(--shadow);
            border-right-color: var(--primary);
        }

        .notif:hover::before {
            opacity: 1;
        }

        .notif.unread {
            background: linear-gradient(135deg, #e8f5e8, #f0f8ff);
            border-right-color: var(--success);
            font-weight: 600;
        }

        .notif.unread::after {
            content: "جديد";
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--success);
            color: var(--white);
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .notif-content {
            position: relative;
            z-index: 2;
        }

        .notif-message {
            margin-bottom: 10px;
            line-height: 1.6;
            color: var(--text);
        }

        .notif-time {
            font-size: 0.85rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .notif-time::before {
            content: "🕒";
            font-size: 1rem;
        }

        /* ===== ÉTAT VIDE ===== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--text);
        }

        .empty-state p {
            font-size: 1.1rem;
        }

        /* ===== ANIMATIONS ===== */
        .notif {
            animation: slideInRight 0.5s ease;
        }

        .notif:nth-child(1) { animation-delay: 0.1s; }
        .notif:nth-child(2) { animation-delay: 0.2s; }
        .notif:nth-child(3) { animation-delay: 0.3s; }
        .notif:nth-child(4) { animation-delay: 0.4s; }
        .notif:nth-child(5) { animation-delay: 0.5s; }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .stat-card {
            animation: slideInUp 0.6s ease;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .page-header {
                padding: 20px;
                margin-bottom: 20px;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-title h1 {
                font-size: 1.8rem;
            }

            .stats-section {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .actions-section {
                flex-direction: column;
                align-items: stretch;
            }

            .search-container {
                min-width: auto;
            }

            .filter-buttons {
                justify-content: center;
            }

            .notifications-header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .notifications-list {
                padding: 15px;
                max-height: 500px;
            }

            .notif {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .header-title h1 {
                font-size: 1.5rem;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-number {
                font-size: 1.5rem;
            }

            .notif {
                padding: 12px;
            }

            .filter-btn {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }

        /* ===== ANIMATIONS SUPPLÉMENTAIRES ===== */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .notif.unread {
            animation: slideInRight 0.5s ease, pulse 2s infinite;
        }
    </style>
</head>
<body>

<div class="page-header">
    <div class="header-content">
        <div class="header-title">
            <h1>إشعاراتي</h1>
        </div>
        <a href="../html/home.php" class="back-link">
            ← العودة للرئيسية
        </a>
    </div>
</div>

<div class="stats-section">
    <div class="stat-card">
        <span class="stat-icon">📊</span>
        <div class="stat-number" id="total-count"><?php echo $total_notifications; ?></div>
        <div class="stat-label">إجمالي الإشعارات</div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">🔔</span>
        <div class="stat-number" id="unread-count"><?php echo $unread_count; ?></div>
        <div class="stat-label">غير مقروءة</div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">✅</span>
        <div class="stat-number" id="read-count"><?php echo $read_count; ?></div>
        <div class="stat-label">مقروءة</div>
    </div>
</div>

<div class="actions-section">
    <div class="search-container">
        <input type="text" class="search-input" id="searchInput" placeholder="البحث في الإشعارات...">
        <span class="search-icon">🔍</span>
    </div>
    <div class="filter-buttons">
        <button class="filter-btn active" data-filter="all">
            <span class="count"><?php echo $total_notifications; ?></span>
            الكل
        </button>
        <button class="filter-btn" data-filter="unread">
            <span class="count"><?php echo $unread_count; ?></span>
            غير مقروءة
        </button>
        <button class="filter-btn" data-filter="read">
            <span class="count"><?php echo $read_count; ?></span>
            مقروءة
        </button>
    </div>
</div>

<div class="notif-container">
    <div class="notifications-header">
        <h3 class="notifications-title">قائمة الإشعارات</h3>
        <span class="notifications-count" id="visible-count"><?php echo $total_notifications; ?> إشعار</span>
    </div>
    
    <div class="notifications-list" id="notificationsList">
        <?php if (count($notifications) > 0): ?>
            <?php foreach ($notifications as $notif): ?>
                <div class="notif <?php echo $notif['lu'] == 0 ? 'unread' : 'read'; ?>" 
                     data-status="<?php echo $notif['lu'] == 0 ? 'unread' : 'read'; ?>"
                     data-message="<?php echo htmlspecialchars($notif['message']); ?>">
                    <div class="notif-content">
                        <div class="notif-message">
                            <?php echo htmlspecialchars($notif['message']); ?>
                        </div>
                        <div class="notif-time">
                            <?php echo date('d/m/Y H:i', strtotime($notif['date_creation'])); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <h3>لا توجد إشعارات</h3>
                <p>لا توجد إشعارات حالياً. ستظهر هنا عند وصول إشعارات جديدة.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const notificationsList = document.getElementById('notificationsList');
    const visibleCount = document.getElementById('visible-count');
    const notifications = document.querySelectorAll('.notif');

    // البحث في الإشعارات
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filterNotifications();
    });

    // فلترة الإشعارات
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            filterNotifications();
        });
    });

    function filterNotifications() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;
        let visibleNotifications = 0;

        notifications.forEach(notification => {
            const message = notification.dataset.message.toLowerCase();
            const status = notification.dataset.status;
            
            const matchesSearch = message.includes(searchTerm);
            const matchesFilter = activeFilter === 'all' || status === activeFilter;
            
            if (matchesSearch && matchesFilter) {
                notification.style.display = 'block';
                visibleNotifications++;
            } else {
                notification.style.display = 'none';
            }
        });

        visibleCount.textContent = visibleNotifications + ' إشعار';

        // إظهار حالة فارغة إذا لم توجد نتائج
        const emptyState = document.querySelector('.empty-state');
        if (visibleNotifications === 0 && !emptyState) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'empty-state';
            emptyDiv.innerHTML = `
                <div class="icon">🔍</div>
                <h3>لا توجد نتائج</h3>
                <p>لم يتم العثور على إشعارات تطابق البحث أو الفلتر المحدد.</p>
            `;
            notificationsList.appendChild(emptyDiv);
        } else if (visibleNotifications > 0 && emptyState) {
            emptyState.remove();
        }
    }

    // تحديث الإشعارات إلى مقروءة عند النقر
    notifications.forEach(notification => {
        notification.addEventListener('click', function() {
            if (this.classList.contains('unread')) {
                this.classList.remove('unread');
                this.classList.add('read');
                this.dataset.status = 'read';
                
                // تحديث العدادات
                const unreadCount = document.getElementById('unread-count');
                const readCount = document.getElementById('read-count');
                
                unreadCount.textContent = parseInt(unreadCount.textContent) - 1;
                readCount.textContent = parseInt(readCount.textContent) + 1;
                
                // تحديث عدادات الأزرار
                document.querySelector('[data-filter="unread"] .count').textContent = unreadCount.textContent;
                document.querySelector('[data-filter="read"] .count').textContent = readCount.textContent;
            }
        });
    });

    // تحديث الإشعارات إلى مقروءة في قاعدة البيانات
    <?php if ($unread_count > 0): ?>
    setTimeout(function() {
        // تحديث الإشعارات إلى "مقروءة" في قاعدة البيانات
        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'mark_as_read=1'
        });
    }, 3000); // بعد 3 ثوان من فتح الصفحة
    <?php endif; ?>
});
</script>

</body>
</html>

<?php
// تحديث الإشعارات إلى "مقروءة" إذا تم إرسال طلب POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_as_read'])) {
    $sql_update = "UPDATE notifications SET lu = 1 WHERE id_user = ? AND lu = 0";
    $stmt_update = $conn->prepare($sql_update);
    if ($stmt_update) {
        $stmt_update->bind_param("i", $id_user);
        $stmt_update->execute();
        $stmt_update->close();
    }
}

$stmt->close();
$conn->close();
?>
