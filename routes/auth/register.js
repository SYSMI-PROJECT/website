const express = require('express');
const router = express.Router();
const bcrypt = require('bcrypt');
const nodemailer = require('nodemailer');
const db = require('../../database');
const dotenv = require('dotenv');
dotenv.config();

function containsEmail(str) {
  return str.includes('@');
}

function isValidEmailDomain(email, allowedDomains) {
  const domain = email.split('@')[1];
  return allowedDomains.includes(domain);
}

router.post('/', async (req, res) => {
  const { nom, prenom, email, motDePasse, pays, date_naissance } = req.body;

  if (!nom || !prenom || !email || !motDePasse || !pays || !date_naissance) {
    return res.status(400).send('All fields are required.');
  }

  if (containsEmail(nom) || containsEmail(prenom)) {
    return res.status(400).send('Name and surname cannot contain an email address.');
  }

  if (motDePasse.length < 8) {
    return res.status(400).send('Password must be at least 8 characters long.');
  }

  const allowedDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com', 'protonmail.com'];
  if (!isValidEmailDomain(email, allowedDomains)) {
    return res.status(400).send("Invalid Email.");
  }

  const [day, month, year] = date_naissance.split('/');
  const birthDate = new Date(`${year}-${month}-${day}`);
  const age = new Date().getFullYear() - birthDate.getFullYear();
  if (age < 13) {
    return res.status(400).send("You must be at least 13 years old to register.");
  }

  try {
    const [existing] = await db.execute('SELECT id FROM utilisateur WHERE email = ?', [email]);
    if (existing.length > 0) {
      return res.redirect('/Import/Error/Mail/Mail_exist');
    }

    const hashedPassword = await bcrypt.hash(motDePasse, 12);
    const verificationToken = require('crypto').randomBytes(16).toString('hex');
    const dateInscription = new Date();

    await db.execute(`
      INSERT INTO utilisateur (nom, prenom, email, password, pays, date_naissance, verification_token, date_inscription)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
      [nom, prenom, email, hashedPassword, pays, birthDate.toISOString().slice(0, 10), verificationToken, dateInscription]
    );

    const transporter = nodemailer.createTransport({
      host: 'smtp.gmail.com',
      port: 587,
      secure: false,
      auth: {
        user: process.env.SMTP_USERNAME,
        pass: process.env.SMTP_PASSWORD,
      },
    });

    const activationLink = `https://sysmiproject.mercurehosting.com/traitements/Formulaires/token.php?token=${verificationToken}`;

    await transporter.sendMail({
      from: `"SYSMI Project" <${process.env.SMTP_USERNAME}>`,
      to: email,
      subject: 'Veuillez activer votre compte',
      html: `
        <div style="font-family: Arial; padding: 20px;">
          <h3>Bonjour ${prenom},</h3>
          <p>Merci pour votre inscription. Cliquez ci-dessous pour activer votre compte :</p>
          <a href="${activationLink}" style="background-color:#007bff; color:white; padding:10px 15px; border-radius:5px;">Activer mon compte</a>
          <p>Si vous n’êtes pas à l’origine de cette demande, ignorez ce message.</p>
        </div>`,
    });

    req.session.nom = nom;
    req.session.email = email;
    req.session.verificationToken = verificationToken;

    return res.redirect('/public/import/Error/Mail/Mail_sended');
  } catch (err) {
    console.error('Register error :', err);
    res.status(500).send('Server error during registration.');
  }
});

module.exports = router;
