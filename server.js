require('dotenv').config();
const express = require('express');
const session = require('express-session');
const mysql = require('mysql2/promise');
const path = require('path');
const cookieParser = require('cookie-parser');

// Initialisation de Express
const app = express();
const PORT = process.env.PORT || 3000;

// Ajouter des variables locales à `app` après l'initialisation de `app`
app.locals.nl2br = function (str) {
  return str.replace(/\n/g, '<br>');
};

app.locals.convertHashtagsToLinks = function (str) {
  return str.replace(/#(\w+)/g, function (match, tag) {
    return `<a href="/search?hashtag=${encodeURIComponent(tag)}">#${tag}</a>`;
  });
};

app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(cookieParser());

app.use(session({
  secret: process.env.SESSION_SECRET || 'secretKey',
  resave: false,
  saveUninitialized: false,
}));

app.use(express.static(path.join(__dirname, 'public')));
app.use('/uploads', express.static(path.join(__dirname, 'public/uploads')));
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

const db = require('./database');

// Middlewares
const checkStayConnected = require('./middleware/checkStayConnected');
const sessionUser = require('./middleware/UsersData');
const getUserProfileImage = require('./middleware/profil');
const getTheme = require('./middleware/get_theme');
const getLanguage = require('./middleware/get_language');
const friends = require('./middleware/counter/friend');

app.use(checkStayConnected);
app.use(sessionUser);
app.use(getUserProfileImage);
app.use(getTheme);
app.use(getLanguage);
app.use(friends);

const authRoutes = require('./routes/auth');
const miscRoutes = require('./routes/miscellaneous');
const settingsRoute = require('./routes/forms');
const uploadRoutes = require('./routes/upload');
const staffRoutes = require('./routes/staff');
const discordRoutes = require('./routes/discord');
const chambreRoute = require('./routes/chambre');
const userAccessRoutes = require("./routes/userAccess");
const cameraRoutes = require("./routes/camera");
const hashtagRoutes = require("./routes/search");

app.use('/auth', authRoutes);
app.use('/settings', settingsRoute);
app.use('/upload', uploadRoutes);
app.use('/staff', staffRoutes);
app.use('/discord', discordRoutes);
app.use('/chambre', chambreRoute);
app.use('/camera', cameraRoutes);
app.use('/search', hashtagRoutes);
app.use(userAccessRoutes);

app.get('/login', (req, res) => {
  const stayConnected = req.cookies.stay_connected ? true : false;
  res.render('auth/login', { stayConnected });
});

app.get('/register', (req, res) => {
  res.render('auth/register');
});

app.get('/', async (req, res) => {
  let conn;

  try {
    conn = await db.getConnection();
    console.log("✅ Connexion à la base de données réussie.");

    let amis = [], nbDemandes = 0, image_content = null, prenom = '', role = '', etoile = '';
    const user = req.userData;

    if (user) {
      const userId = user.id;

      console.log("📥 Requête des amis...");
      const [rows] = await conn.execute(
        `SELECT u.id, u.prenom, u.photo_profil
         FROM utilisateur u
         JOIN relation r ON (r.demandeur = u.id OR r.receveur = u.id)
         WHERE (r.demandeur = ? OR r.receveur = ?)
         AND r.statut = 1
         AND u.id != ?`,
        [userId, userId, userId]
      );

      amis = rows.map(ami => ({
        id: ami.id,
        prenom: ami.prenom,
        photo_profil: ami.photo_profil || null
      }));

      prenom = user.prenom;
      role = user.role;
      etoile = user.etoile ?? 0;
      image_content = user.photo_profil || null;

      console.log("📥 Requête des demandes en attente...");
      const [notifRes] = await conn.execute(
        'SELECT COUNT(*) as count FROM relation WHERE receveur = ? AND statut = 0',
        [userId]
      );

      nbDemandes = notifRes[0].count;
    }

    res.render('index', {
      isUserLoggedIn: !!user,
      amis,
      nbDemandes,
      image_content,
      prenom,
      etoile,
      role,
      cssFile: res.locals.cssFile,
      user_id: user ? user.id : null,
      user,
      userData: {
        photo_profil: image_content
      }
    });

  } catch (error) {
    console.error("❌ Erreur dans la route / :", error);
    res.status(500).send("Erreur serveur.");
  } finally {
    if (conn) conn.release();
  }
});

app.get('/logout', (req, res) => {
  res.clearCookie('stay_connected');
  req.session.destroy(() => {
    res.redirect('/');
  });
});

app.get('/privacy', (req, res) => {
  res.render('privacy');
});

// Autres routes
app.use('/', miscRoutes);

app.listen(PORT, () => {
  console.log(`✅ Serveur lancé sur http://localhost:${PORT}`);
});
