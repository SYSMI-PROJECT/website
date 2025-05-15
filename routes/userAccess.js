const express = require("express");
const router = express.Router();
const db = require("../database");
const UserData = require('../middleware/UsersData'); // 🔐 Middleware activé

// 🔓 Accès déblocables par badge
const accesParBadge = {
  gamer:         { lien: "/mini-jeux-plus", label: "Mini-jeux+",      icone: "🎮" },
  vip:           { lien: "/vip-zone",       label: "Zone VIP",        icone: "💎" },
  explorateur:   { lien: "/decouverte",     label: "Découverte",      icone: "🧭" },
  collectionneur:{ lien: "/objets-rares",   label: "Objets Rares",    icone: "🧸" }
};

// 🔐 Route pour récupérer les accès selon les badges de l'utilisateur
router.get("/user/acces", UserData, async (req, res) => {
  const userID = req.session.userId;

  if (!userID) {
    return res.status(401).json({ succes: false, message: "Non autorisé" });
  }

  try {
    const [badges] = await db.execute(
      "SELECT badge_nom FROM badges_utilisateur WHERE user_id = ?",
      [userID]
    );

    const accesDebloques = badges
      .filter(b => accesParBadge[b.badge_nom])
      .map(b => ({
        badge: b.badge_nom,
        ...accesParBadge[b.badge_nom]
      }));

    return res.json({
      succes: true,
      acces: accesDebloques,
      userData: req.userData // si tu veux l’exploiter côté client
    });

  } catch (err) {
    console.error("❌ Erreur API /user/acces :", err);
    return res.status(500).json({ succes: false, message: "Erreur serveur" });
  }
});

module.exports = router;
