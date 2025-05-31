<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Aide & Contact</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <style>
    /* Aide & Contact Section */
body {
  font-family: 'Segoe UI', sans-serif;
  background-color: #ffffff;
  margin: 0;
  padding: 0;
  color: #000000; /* النص العام أسود */
}

.container {
  max-width: 1100px;
  margin: 40px auto;
  padding: 0 20px;
}

h2, h3, label, strong {
  color: #ff6f00; /* برتقالي للعناوين والعناصر البارزة */
}

p, li {
  color: #000000; /* نص عادي بالأسود */
}

/* الأسئلة الشائعة */
.faq li {
  background-color: #fff3e0;
  border-left: 4px solid #ff6f00;
  margin-bottom: 20px;
  padding: 15px 20px;
  border-radius: 10px;
  color: #000; /* النص داخل المربعات بالأسود */
}

/* النموذج */
.contact-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group label {
  font-weight: 700;
  margin-bottom: 5px;
  display: block;
  color: #ff6f00;
}

.form-group input,
.form-group textarea {
  padding: 12px 16px;
  border: 2px solid #ff6f00;
  border-radius: 12px;
  font-size: 16px;
  width: 100%;
  color: #000; /* نص داخل الخانات */
  background-color: #fff;
  transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #e65c00;
}

/* زر الإرسال */
.btn-modifier {
  background-color: #ff6f00;
  color: white;
  padding: 12px 20px;
  font-size: 16px;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-modifier:hover {
  background-color: #e65c00;
}

  </style>
  <div class="container">
    <main>
      <section class="aide-section">
        <h2>Centre d'Aide</h2>
        <p>Vous avez une question ? Nous sommes là pour vous aider. Consultez les questions fréquentes ou contactez-nous directement.</p>
        
        <div class="faq">
          <h3>Questions Fréquentes</h3>
          <ul>
            <li><strong>Comment suivre ma commande ?</strong> <br>Connectez-vous à votre compte puis accédez à "Mes commandes".</li>
            <li><strong>Comment modifier mes informations personnelles ?</strong> <br>Allez dans "Profil" et cliquez sur "Modifier".</li>
            <li><strong>Quels sont les moyens de paiement acceptés ?</strong> <br>Nous acceptons les paiements par carte bancaire, PayPal et virement.</li>
          </ul>
        </div>
      </section>

      <section class="contact-section">
        <h2>Contactez-nous</h2>
        <form class="contact-form" action="#" method="post">
          <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" id="name" name="name" placeholder="Votre nom" required>
          </div>
          <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email" placeholder="Votre e-mail" required>
          </div>
          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" placeholder="Votre message..." required></textarea>
          </div>
          <button type="submit" class="btn-modifier">Envoyer</button>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
