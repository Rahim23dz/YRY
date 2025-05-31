// Obtenir les éléments
const modal = document.getElementById("loginModal");
const loginTab = document.getElementById("loginTab");
const signupTab = document.getElementById("signupTab");
const loginForm = document.getElementById("loginForm");
const signupForm = document.getElementById("signupForm");
const modalTitle = document.getElementById("modalTitle");
const btn = document.getElementById("loginButton");
const span = document.getElementsByClassName("close")[0];
const btn2 = document.getElementById("loginButton2");

// Bouton principal "Se connecter"
if (btn) {
  btn.onclick = function () {
    modal.style.display = "block";
    loginTab.click();
  };
}

// Bouton dans les 3 points
if (btn2) {
  btn2.onclick = function () {
    modal.style.display = "block";
    loginTab.click();
  };
}

// Fermer la modale
if (span) {
  span.onclick = function () {
    modal.style.display = "none";
  };
}

// Fermer la modale si clic en dehors
window.onclick = function (event) {
  if (event.target === modal) {
    modal.style.display = "none";
  }
};

// Tabs switching
if (loginTab && signupTab && loginForm && signupForm && modalTitle) {
  loginTab.onclick = function () {
    loginForm.style.display = "block";
    signupForm.style.display = "none";
    loginTab.classList.add("active");
    signupTab.classList.remove("active");
    modalTitle.textContent = "Connexion";
  };

  signupTab.onclick = function () {
    signupForm.style.display = "block";
    loginForm.style.display = "none";
    signupTab.classList.add("active");
    loginTab.classList.remove("active");
    modalTitle.textContent = "Inscription";
  };
}

// Soumissions des formulaires
if (loginForm) {
  loginForm.addEventListener("submit", function (event) {
    event.preventDefault();
    alert("Connexion réussie !");
    modal.style.display = "none";
  });
}

if (signupForm) {
  signupForm.addEventListener("submit", function (event) {
    event.preventDefault();
    alert("Inscription réussie !");
    modal.style.display = "none";
  });
}

// Toggle menu 3 points
const menuToggle = document.getElementById("menuToggle");
const menuOptions = document.getElementById("menuOptions");

if (menuToggle && menuOptions) {
  menuToggle.addEventListener("click", () => {
    menuOptions.classList.toggle("show");
  });

  window.addEventListener("click", function (e) {
    if (!menuToggle.contains(e.target) && !menuOptions.contains(e.target)) {
      menuOptions.classList.remove("show");
    }
  });
}

// Scroll fluide pour les ancres
document.querySelectorAll('nav a[href^="#"]').forEach(anchor => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if(target){
      target.scrollIntoView({
        behavior: "smooth",
      });
    }
  });
});

// Ajout dynamique de la liste des produits (si présent)
document.addEventListener('DOMContentLoaded', function () {
  const produits = document.querySelectorAll('.produit h4');
  const liste = document.getElementById('tous-produits');

  if(liste){
    produits.forEach(produit => {
      const nom = produit.textContent;
      const li = document.createElement('li');
      li.textContent = nom;
      liste.appendChild(li);
    });
  }
});

// Recherche de produits
const searchBar = document.getElementById("searchBar");
const produits = document.querySelectorAll(".produit");
const noResultMessage = document.getElementById('noResultMessage');

if (searchBar && produits.length > 0) {
  searchBar.addEventListener("input", function () {
    const searchTerm = searchBar.value.toLowerCase().trim();
    let found = false;

    produits.forEach(produit => {
      const titleElement = produit.querySelector("h4");
      if (!titleElement) return;

      const title = titleElement.textContent.toLowerCase();

      if (title.includes(searchTerm)) {
        produit.style.display = "";
        found = true;
      } else {
        produit.style.display = "none";
      }
    });

    if (noResultMessage) {
      noResultMessage.style.display = found ? 'none' : 'block';
    }
  });
}
