<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paiement en attente</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            color: #fafafa;
            background: #12090e;
        }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 10% 20%, #ff5c7433, transparent 55%),
                        radial-gradient(circle at 80% 0%, #642cf233, transparent 45%),
                        linear-gradient(135deg, #050307, #1a0f1a 65%, #09040c);
            color: #ffe1ee;
        }
        .card {
            width: min(520px, 90vw);
            padding: 2.5rem 2.25rem;
            border-radius: 1.75rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(15, 4, 11, 0.8);
            backdrop-filter: blur(12px);
            text-align: center;
        }
        h1 {
            margin-top: 0.5rem;
            font-size: clamp(2rem, 5vw, 3rem);
            color: #ffb3c7;
        }
        p {
            color: #ffd5e2;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ff7a99;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 0.85rem;
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }
        .cta {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem 1.8rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: transform 0.2s ease;
        }
        .cta.primary {
            background: #ff6b8b;
            color: #2b0a18;
        }
        .cta.outline {
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #ffe8f1;
        }
        .cta:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
<div class="card">
    <div class="badge">Paiement non confirmé</div>
    <h1>On n'a pas reçu la validation</h1>
    <p>
        Ta session FedaPay a été interrompue ou est toujours en attente. Rouvre le bot Telegram, tape /status puis ton numéro de commande pour voir l'état exact.
        Si tu as été débité, notre équipe régularise manuellement en moins d'une heure.
    </p>
    <div class="actions">
        <a class="cta primary" href="https://t.me/bottelegramshop_bot?start=retry" target="_blank" rel="noopener">Réessayer le paiement</a>
        <a class="cta outline" href="https://t.me/bitcointobillions" target="_blank" rel="noopener">Contacter le support</a>
    </div>
</div>
</body>
</html>
