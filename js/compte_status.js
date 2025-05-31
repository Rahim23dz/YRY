document.addEventListener('DOMContentLoaded', () => {
    const statusSpan = document.getElementById("compte-status");
    if (!statusSpan) {
        console.error('❌ عنصر #compte-status غير موجود في الصفحة!');
        return;
    }

    // هنا نقرأ الـ vendeurId من data attribute بدل القيمة الثابتة
    const vendeurId = statusSpan.getAttribute('data-vendeur-id');
    if (!vendeurId) {
        console.error('❌ معرف البائع غير محدد!');
        return;
    }

    let isAccepted = false;
    let timeoutId = null;

    function fetchStatus() {
        console.log("🚀 بدء جلب الحالة من السيرفر...");

        fetch(`../php/get_status_vendeur.php?id_vendeur=${vendeurId}`)
            .then(response => {
                console.log("📡 رد السيرفر وصل، حالة الرد:", response.status);
                return response.json();
            })
            .then(data => {
                console.log("📊 بيانات الحالة:", data);

                if (!data || (!data.status && !data.error)) {
                    console.error("⚠️ الرد غير متوقع:", data);
                    return;
                }

                if (data.error) {
                    console.error("⚠️ خطأ من API:", data.error);
                    return;
                }

                const status = data.status;
                let displayText = "";
                let statusClass = "";

                if (status === "accepte") {
                    displayText = "Accepté";
                    statusClass = "accepté";

                    if (!isAccepted) {
                        isAccepted = true;
                        console.log("✅ الحالة مقبولة، سيتم تغييرها إلى 'refuse' بعد 30 ثانية...");

                        if (timeoutId) clearTimeout(timeoutId);

                        timeoutId = setTimeout(() => {
                            console.log("⏰ 30 ثانية انتهت، تغيير الحالة إلى 'refuse' في قاعدة البيانات");
                            changeStatusToRefuse();
                        }, 30000);
                    }

                } else {
                    if (timeoutId) {
                        clearTimeout(timeoutId);
                        timeoutId = null;
                    }

                    if (status === "refuse") {
                        displayText = "Refusé";
                        statusClass = "refusé";
                    } else {
                        displayText = "En attente";
                        statusClass = "en_attente";
                    }
                    isAccepted = false;
                }

                updateStatusUI(displayText, statusClass);
            })
            .catch(error => {
                console.error("🚨 خطأ في جلب الحالة:", error);
            });
    }

    function updateStatusUI(text, className) {
        statusSpan.textContent = text;
        statusSpan.className = "";
        statusSpan.classList.add("status", className);
    }

    function changeStatusToRefuse() {
        console.log("🚀 بدء طلب تغيير الحالة إلى 'refuse' في قاعدة البيانات");

        fetch('../php/change_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_vendeur=${encodeURIComponent(vendeurId)}&action=refuse`
        })
        .then(response => {
            console.log("📡 رد السيرفر على تغيير الحالة:", response.status);
            return response.json();
        })
        .then(data => {
            console.log("📊 رد تغيير الحالة:", data);

            if (data.success) {
                updateStatusUI("Refusé", "refusé");
                isAccepted = false;
                timeoutId = null;
                console.log("✅ تم تغيير الحالة في قاعدة البيانات إلى 'Refusé'");

                if (data.logout) {
                    console.log("🚪 تسجيل خروج المستخدم حسب طلب السيرفر");
                    window.location.href = '../logout.php';
                }

            } else {
                console.error("❌ فشل تغيير الحالة:", data.message);
            }
        })
        .catch(error => {
            console.error("🚨 خطأ أثناء تغيير الحالة:", error);
        });
    }

    fetchStatus();
    setInterval(fetchStatus, 30000);
});

