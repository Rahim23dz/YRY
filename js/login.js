// تبويبات تسجيل الدخول والتسجيل
const loginTab = document.getElementById('loginTab');
const signupTab = document.getElementById('signupTab');
const loginForm = document.getElementById('loginForm');
const signupForm = document.getElementById('signupForm');

const signupUser = document.getElementById('signupUser');
const signupEmail = document.getElementById('signupEmail');
const signupPhone = document.getElementById('signupPhone');
const signupPass = document.getElementById('signupPass');
const signupImage = document.getElementById('signupImage');
const signupRole = document.getElementById('signupRole');
const signupAdresse = document.getElementById('signupAdresse');
const signupWilaya = document.getElementById('signupWilaya');

// تعريف الحقول الخاصة بالبائع
const storeNameInput = document.getElementById('storeName');
const storeAddressInput = document.getElementById('storeAddress');

const messageDiv = document.getElementById('message');

// دالة لعرض الرسائل في الصفحة بدل alert
function showMessage(msg, type = 'info') {
  messageDiv.textContent = msg;
  if (type === 'error') {
    messageDiv.style.color = 'red';
  } else if (type === 'success') {
    messageDiv.style.color = 'green';
  } else {
    messageDiv.style.color = 'black';
  }
}

// التبديل بين النموذجين
function switchToLogin() {
  loginTab.classList.add('active');
  signupTab.classList.remove('active');
  loginForm.style.display = 'flex';
  signupForm.style.display = 'none';
  messageDiv.textContent = ''; // إزالة أي رسالة عند التبديل
}

function switchToSignup() {
  signupTab.classList.add('active');
  loginTab.classList.remove('active');
  signupForm.style.display = 'flex';
  loginForm.style.display = 'none';
  messageDiv.textContent = '';
}

loginTab.addEventListener('click', switchToLogin);
signupTab.addEventListener('click', switchToSignup);

// معالجة تسجيل الدخول
loginForm.addEventListener('submit', async (e) => {
  e.preventDefault();

  const username = document.getElementById('loginUser').value.trim();
  const password = document.getElementById('loginPass').value.trim();

  if (!username || !password) {
    showMessage("يرجى ملء جميع الحقول.", 'error');
    return;
  }

  try {
    const response = await fetch('../php/login.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `loginUser=${encodeURIComponent(username)}&loginPass=${encodeURIComponent(password)}`
    });

    if (!response.ok) {
      showMessage("حدث خطأ أثناء إرسال الطلب.", 'error');
      return;
    }

    const rawRole = (await response.text()).trim().toLowerCase();

    console.log("الرد من السيرفر:", rawRole);

    switch (rawRole) {
      case 'client':
      case 'vendeur':
      case 'accepté':
      case 'accepter':
        window.location.href = '../html/home.php';
        break;
      case 'refuser':
        window.location.href = '../php/envoyer_recus.php';
        break;
      case 'en_attente':
        showMessage("⏳ حسابك كبائع قيد المراجعة.", 'info');
        break;
      default:
        showMessage('❌ خطأ في معلومات الدخول.', 'error');
    }

  } catch (error) {
    console.error('خطأ:', error);
    showMessage("حدث خطأ أثناء عملية الدخول.", 'error');
  }
});

// معالجة التسجيل
signupForm.addEventListener('submit', async (e) => {
  e.preventDefault();

  const signupUserValue = signupUser.value.trim();
  const signupEmailValue = signupEmail.value.trim();
  const signupPhoneValue = signupPhone.value.trim();
  const signupPassValue = signupPass.value.trim();
  const signupImageFile = signupImage.files[0];
  const signupRoleValue = signupRole.value;
  const signupAdresseValue = signupAdresse.value.trim();
  const signupWilayaValue = signupWilaya.value;

  if (!signupUserValue || !signupEmailValue || !signupPhoneValue || !signupPassValue || !signupRoleValue) {
    showMessage("يرجى ملء جميع الحقول الإجبارية.", 'error');
    return;
  }

  if (!signupAdresseValue || !signupWilayaValue) {
    showMessage("يرجى ملء العنوان الكامل والولاية.", 'error');
    return;
  }

  if (signupRoleValue === 'vendeur') {
    const storeNameValue = storeNameInput.value.trim();
    const storeAddressValue = storeAddressInput.value.trim();

    if (!storeNameValue || !storeAddressValue) {
      showMessage("يرجى ملء اسم وعنوان المتجر.", 'error');
      return;
    }
  }

  // تحقق نوع وحجم الصورة
  if (signupImageFile) {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    const maxSize = 2 * 1024 * 1024; // 2 ميغا

    if (!allowedTypes.includes(signupImageFile.type)) {
      showMessage("الصورة يجب أن تكون بصيغة jpg أو png أو gif فقط.", 'error');
      return;
    }

    if (signupImageFile.size > maxSize) {
      showMessage("حجم الصورة لا يجب أن يتجاوز 2 ميغا.", 'error');
      return;
    }
  }

  const formData = new FormData();
  formData.append('signupUser', signupUserValue);
  formData.append('signupEmail', signupEmailValue);
  formData.append('signupPhone', signupPhoneValue);
  formData.append('signupPass', signupPassValue);
  formData.append('signupRole', signupRoleValue);
  formData.append('signupAdresse', signupAdresseValue);
  formData.append('signupWilaya', signupWilayaValue);

  if (signupImageFile) {
    formData.append('signupImage', signupImageFile);
  }

  if (signupRoleValue === 'vendeur') {
    formData.append('storeName', storeNameInput.value.trim());
    formData.append('storeAddress', storeAddressInput.value.trim());
  }

  try {
    const response = await fetch('../php/signup.php', {
      method: 'POST',
      body: formData
    });

    if (!response.ok) {
      showMessage("حدث خطأ أثناء إرسال طلب التسجيل.", 'error');
      return;
    }

    const result = await response.text();

    if (result.trim() === "success") {
      showMessage("تم التسجيل بنجاح!", 'success');
      switchToLogin();
      signupForm.reset();
    } else {
      showMessage("خطأ: " + result, 'error');
    }
  } catch (error) {
    console.error('خطأ:', error);
    showMessage("حدث خطأ أثناء عملية التسجيل.", 'error');
  }
});
// إظهار أو إخفاء حقول البائع حسب الدور
signupRole.addEventListener('change', () => {
  const vendeurFields = document.getElementById('vendeurFields'); // هذا عنصر يحيط ب storeName و storeAddress

  if (signupRole.value === 'vendeur') {
    vendeurFields.style.display = 'block';
  } else {
    vendeurFields.style.display = 'none';
  }
});

// تأكيد الحالة الأولية عند تحميل الصفحة
window.addEventListener('DOMContentLoaded', () => {
  const vendeurFields = document.getElementById('vendeurFields');
  if (signupRole.value === 'vendeur') {
    vendeurFields.style.display = 'block';
  } else {
    vendeurFields.style.display = 'none';
  }
});
