<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bot Telegram Shop</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            font-family: 'Space Grotesk', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #0b0c0d;
            background: #050507;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(circle at 10% 20%, #1d1a4d, transparent 50%),
                        radial-gradient(circle at 80% 0%, #5a1b56, transparent 45%),
                        linear-gradient(135deg, #050507 0%, #0e1013 60%, #0a0b12 100%);
            color: #f5f5f7;
        }
        .page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 3rem 1.5rem 4rem;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }
        header span {
            text-transform: uppercase;
            letter-spacing: 0.2em;
            font-size: 0.85rem;
            color: #8f99ff;
        }
        .hero {
            margin-top: 4rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            align-items: center;
        }
        .hero h1 {
            font-size: clamp(2.2rem, 6vw, 4rem);
            line-height: 1.1;
            margin: 0 0 1rem;
        }
        .hero p {
            color: #cfd3ff;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .cta-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .cta {
            border: none;
            cursor: pointer;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.95rem 1.9rem;
            font-weight: 600;
            border-radius: 999px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .cta.primary {
            background: linear-gradient(120deg, #f76b1c, #fca13b);
            color: #0b0c0d;
            box-shadow: 0 10px 30px rgba(249, 133, 63, 0.35);
        }
        .cta.secondary {
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #f5f5f7;
        }
        .cta:hover { transform: translateY(-2px); }
        .card-grid {
            margin-top: 4rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }
        .card {
            padding: 1.75rem;
            border-radius: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.06);
            background: rgba(9, 11, 19, 0.75);
            backdrop-filter: blur(16px);
        }
        .card h3 { margin: 0 0 0.8rem; }
        .timeline {
            margin-top: 4rem;
            display: grid;
            gap: 1rem;
        }
        .timeline-step {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }
        .step-index {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        footer {
            margin-top: 4rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 1.5rem;
            font-size: 0.9rem;
            color: #acb3ff;
        }
    </style>
</head>
<body>
<div class="page">
    <header>
        <span>BOT·SHOP·TELEGRAM</span>
        <span>Support : @bitcointobillions</span>
    </header>

    <section class="hero">
        <div>
            <h1>Automatise la vente de tes kits digitaux directement sur Telegram.</h1>
            <p>
                Sélection du produit, paiement FedaPay sécurisé, livraison automatique du fichier.
                Tout se déroule dans la même conversation Telegram, sans site compliqué à gérer.
            </p>
            <div class="cta-group">
                <a class="cta primary" href="https://t.me/bottelegramshop_bot?start=shop" target="_blank" rel="noopener">
                    Lancer le bot
                </a>
                <a class="cta secondary" href="https://t.me/bottelegramshop_bot" target="_blank" rel="noopener">
                    Voir une démo
                </a>
            </div>
        </div>
        <div class="card">
            <h3>Statut en temps réel</h3>
            <p>
                La commande est créée avec un numéro unique (ex : TGDT7PNXFY). Tu peux vérifier son état à tout moment avec la commande /status dans Telegram.
            </p>
            <p>
                Paiement validé = fichier livré automatiquement. Si un incident survient, tu reçois une alerte immédiate pour intervenir.
            </p>
        </div>
    </section>

    <section class="card-grid">
        <article class="card">
            <h3>1 · Boutique</h3>
            <p>Commandes /shop et /buy pour parcourir les offres et déclencher un achat en quelques touches.</p>
        </article>
        <article class="card">
            <h3>2 · Paiement</h3>
            <p>FedaPay (mobile money, carte) avec signature vérifiée côté serveur, retours webhook et logs détaillés.</p>
        </article>
        <article class="card">
            <h3>3 · Livraison</h3>
            <p>Envoi instantané du PDF, template ou archive depuis le stockage sécurisé une fois le paiement confirmé.</p>
        </article>
    </section>

    <section class="timeline">
        <div class="timeline-step">
            <div class="step-index">01</div>
            <div>
                <h3>Configure tes produits</h3>
                <p>Ajoute ton fichier dans storage/app/products et mets à jour la base. Le bot affiche automatiquement les kits disponibles.</p>
            </div>
        </div>
        <div class="timeline-step">
            <div class="step-index">02</div>
            <div>
                <h3>Brancher les clés</h3>
                <p>Token Telegram, clés FedaPay et URL du webhook sont définis dans .env. Les transactions restent dans ton compte.</p>
            </div>
        </div>
        <div class="timeline-step">
            <div class="step-index">03</div>
            <div>
                <h3>Suivre et analyser</h3>
                <p>Chaque étape déclenche un log structuré (telegram.update, fedapay.transaction_created, order.payment_synced). Tu sais exactement où se trouve ton client.</p>
            </div>
        </div>
    </section>

    <footer>
        © {{ date('Y') }} Telegram Shop · Paiement opéré par FedaPay · Hébergé sur Hostinger · Support 7j/7 directement sur Telegram.
    </footer>
</div>
</body>
</html>
