 function handleGameAction(url, releaseDate) {
            const today = new Date();
            const release = new Date(releaseDate);
            if (today < release) {
                alert('Este juego aún no está disponible. Puedes pre-ordenarlo.');
            } else {
                window.open(url, '_blank');
            }
        }