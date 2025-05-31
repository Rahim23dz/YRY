// بيانات افتراضية للمنتجات (يمكن تعويضها ببيانات من localStorage أو الخادم)
const produits = [
    { nom: "Clé à bougie", prix: 1200, quantite: 2 },
    { nom: "Filtre à huile", prix: 800, quantite: 1 },
    { nom: "Plaquettes de frein", prix: 2500, quantite: 1 }
];

// عناصر من الصفحة
const panierBody = document.getElementById("panierBody");
const totalPrixEl = document.getElementById("totalPrix");

function afficherProduits() {
    panierBody.innerHTML = "";
    let total = 0;

    produits.forEach((produit, index) => {
        const totalProduit = produit.prix * produit.quantite;
        total += totalProduit;

        const row = document.createElement("tr");

        row.innerHTML = `
            <td>${produit.nom}</td>
            <td>${produit.prix} DZD</td>
            <td>${produit.quantite}</td>
            <td>${totalProduit} DZD</td>
            <td><button onclick="supprimerProduit(${index})">🗑️</button></td>
        `;

        panierBody.appendChild(row);
    });

    totalPrixEl.textContent = `${total} DZD`;
}

function supprimerProduit(index) {
    produits.splice(index, 1);
    afficherProduits();
}

// تنفيذ عند تحميل الصفحة
window.onload = afficherProduits;
