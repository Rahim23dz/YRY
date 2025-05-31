

document.addEventListener('DOMContentLoaded', function () {
  const productsContainer = document.getElementById('products-container');
  const sousType = productsContainer.dataset.sousType;
  let userRole = document.body.dataset.userRole || 'guest';

  function getEtatClass(etat) {
    etat = parseInt(etat);
    if (etat >= 1 && etat <= 4) return 'etat-red';
    if (etat >= 5 && etat <= 7) return 'etat-orange';
    if (etat >= 8 && etat <= 10) return 'etat-green';
    return 'etat-default';
  }

  function loadProducts(vehicule = 'all', wilaya = 'all') {
    const prixMin = document.getElementById('prix_min')?.value || '';
    const prixMax = document.getElementById('prix_max')?.value || '';

    const data = new URLSearchParams();
    data.append('vehicule', vehicule);
    data.append('wilaya', wilaya);
    data.append('sous_type', sousType);
    if (prixMin !== '') data.append('prix_min', prixMin);
    if (prixMax !== '') data.append('prix_max', prixMax);

    productsContainer.innerHTML = '<p style="text-align:center;">⏳ جاري تحميل المنتجات...</p>';

    fetch('../php/fetch_produits.php', {
      method: 'POST',
      body: data
    })
      .then(response => response.json())
      .then(data => {
        userRole = data.role || userRole;
        const products = data.products || [];

        let html = '';
        if (!products.length) {
          html = '<p style="text-align:center;">لا توجد منتجات.</p>';
        } else {
          products.forEach(p => {
            const etat = p.etat ?? 0;
            const etatClass = getEtatClass(etat);

            let buttonHtml = '';
            if (userRole === 'vendeur') {
              buttonHtml = `
                <button type="button" class="btn-ajouter-panier" disabled
                  title="Vous devez être un client pour ajouter au panier"
                  style="background-color:#ccc; cursor:not-allowed; padding: 8px 12px; font-size: 14px; border-radius: 5px;">
                  إضافة إلى السلة
                </button>
              `;
            } else if (userRole === 'guest') {
              buttonHtml = `
                <button type="button" class="btn-se-connecter"
                  onclick="window.location.href='../html/login.php'"
                  style="background-color:#e67e00; color:#fff; border:none; padding:8px 12px; font-size:14px; border-radius:5px; cursor:pointer; transition: background-color 0.3s ease;">
                  تسجيل الدخول
                </button>
              `;
            } else if (userRole === 'client') {
              buttonHtml = `
                <button type="button" class="btn-ajouter-panier"
                  data-id="${p.id_product}"
                  data-nom="${p.nom}"
                  data-prix="${p.prix}"
                  data-quantite="1"
                  style="padding: 8px 12px; font-size: 14px; border-radius: 5px; cursor: pointer;">
                  إضافة إلى السلة
                </button>
              `;
            } else {
              buttonHtml = `
                <button type="button" class="btn-se-connecter"
                  onclick="window.location.href='../html/login.php'"
                  style="background-color:#e67e00; color:#fff; border:none; padding:8px 12px; font-size:14px; border-radius:5px; cursor:pointer; transition: background-color 0.3s ease;">
                  تسجيل الدخول
                </button>
              `;
            }

            html += `
              <div class="product" style="border:1px solid #ddd; padding:10px; margin-bottom:10px;">
                <h3>${p.nom}</h3>
                <img src="../uploads/products/${p.image ?? 'default.png'}" 
     alt="صورة ${p.nom}" 
     style="width: 100%; height: 200px; object-fit: cover; display: block; margin: 0 auto 8px;">

                <p>PRIX:</strong> ${parseFloat(p.prix).toFixed(2)} دج</p>
                <p>DESCREPTION:</strong>  ${p.description ?? ''}</p>
                <p><strong>NUM VENDEUR:</strong> ${p.phone ?? 'غير متوفر'}</p>
                <p><strong>VENDEUR:</strong> ${p.username ?? 'غير متوفر'}</p>
                <p><strong>REGION</strong> ${p.nom_wilaya ?? 'غير متوفرة'}</p>
                <p><strong>ETAT:</strong> <span class="${etatClass}">${etat} / 10</span></p>
                ${buttonHtml}
              </div>
            `;
          });
        }
        productsContainer.innerHTML = html;
      })
      .catch(error => {
        productsContainer.innerHTML = '<p style="text-align:center; color:red;">⚠️ حدث خطأ أثناء تحميل المنتجات.</p>';
        console.error('Fetch error:', error);
      });
  }

  function onFilterChange() {
    const vehicule = document.getElementById('vehicule')?.value || 'all';
    const wilaya = document.getElementById('wilaya')?.value || 'all';
    loadProducts(vehicule, wilaya);
  }

  document.getElementById('vehicule')?.addEventListener('change', onFilterChange);
  document.getElementById('wilaya')?.addEventListener('change', onFilterChange);
  document.getElementById('filtrerBtn')?.addEventListener('click', onFilterChange);

  loadProducts();

  productsContainer.addEventListener('click', function (event) {
    const target = event.target;
    if (target.classList.contains('btn-ajouter-panier') && !target.disabled) {
      const id_product = target.dataset.id;
      const nom = target.dataset.nom;
      const prix = target.dataset.prix;
      const quantite = target.dataset.quantite || 1;

      const data = new URLSearchParams();
      data.append('id_product', id_product);
      data.append('nom', nom);
      data.append('prix', prix);
      data.append('quantite', quantite);

      fetch('../php/ajouter_panier.php', {
        method: 'POST',
        body: data,
      })
        .then(response => response.text())
        .then(() => alert("✅ تم إضافة المنتج إلى السلة!"))
        .catch(() => alert("⛔ حدث خطأ أثناء إضافة المنتج إلى السلة."));
    }
});
});
