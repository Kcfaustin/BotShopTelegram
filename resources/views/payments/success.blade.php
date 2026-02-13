<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paiement confirmé</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            color: #0b0c0d;
            background: #0a1214;
        }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 20% 20%, #42f19e33, transparent 50%),
                        radial-gradient(circle at 80% 0%, #23cfd333, transparent 40%),
                        linear-gradient(135deg, #061016, #0e1f23 65%, #071214);
            color: #f4fff9;
        }
        .card {
            width: min(520px, 90vw);
            padding: 2.75rem 2.25rem;
            border-radius: 1.75rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(3, 20, 22, 0.75);
            backdrop-filter: blur(12px);
            text-align: center;
        }
        h1 {
            margin-top: 0.5rem;
            font-size: clamp(2rem, 5vw, 3rem);
        }
        p {
            color: #cde8d5;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #9af5cb;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 0.85rem;
        }
        .cta {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.25rem;
            padding: 0.85rem 1.8rem;
            border-radius: 999px;
            background: #47f2a8;
            color: #062317;
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: transform 0.2s ease;
        }
        .cta:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
<div class="card">
    <div class="badge">Paiement validé</div>
    <h1>Merci ! Ton fichier est en route.</h1>
    <p>
        Nous avons confirmé ton paiement FedaPay. Le bot Telegram vient d'expédier le fichier directement dans votre discussion.
        Garde la conversation ouverte pour retrouver ta facture et l'historique de commande.
    </p>
    <p>Besoin d'aide ? Contacte le support sur Telegram : @bitcointobillions.</p>
    <a class="cta" href="https://t.me/bottelegramshop_bot" target="_blank" rel="noopener">Retourner sur Telegram</a>
</div>
</body>
</html>
