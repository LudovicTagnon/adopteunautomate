function fetchParticipants(trajetId) {
    fetch(`/note_participants/${trajetId}`)
        .then(response => response.json())
        .then(data => displayParticipants(data));
}

function displayParticipants(participants) {
    const participantsList = document.getElementById('selectParticipantsList');

    if (participantsList) {
        // Remove existing options
        while (participantsList.firstChild) {
            participantsList.removeChild(participantsList.firstChild);
        }

        participants.forEach(participant => {
            const optionItem = document.createElement('option');
            optionItem.value = participant.id;
            optionItem.textContent = `${participant.prenom} ${participant.nom}`;
            if (participant.selected) {
                optionItem.selected = true;
            }
            participantsList.appendChild(optionItem);
        });

        // Make the participantsList and confirmParticipant button visible
        participantsList.style.display = 'block';
        document.getElementById('confirmParticipant').style.display = 'block';
    } else {
        console.error('selectParticipantsList element not found');
    }
}

function displayRatingStars() {
    const ratingContainer = document.getElementById('ratingContainer');
    const ratingStars = document.getElementById('ratingStars');

    if (ratingContainer && ratingStars) {
        ratingStars.innerHTML = '';

        for (let i = 1; i <= 5; i++) {
            const star = document.createElement('input');
            star.type = 'radio';
            star.name = 'rating';
            star.value = i;
            star.id = `star${i}`;
            star.classList.add('rating-star');

            const starLabel = document.createElement('label');
            starLabel.htmlFor = `star${i}`;
            starLabel.innerHTML = '&#9733;'; // Unicode for a star
            starLabel.classList.add('rating-label');

            ratingStars.appendChild(star);
            ratingStars.appendChild(starLabel);
        }

        // Show the ratingContainer
        ratingContainer.style.display = 'block';
    } else {
        console.error('ratingContainer or ratingStars element not found');
    }
}

$(document).ready(function() {
    $('#form_trajet').submit(function(event) {
        event.preventDefault();
        var trajetId = $(':input.trajet-id').val();

        // Add a console log to check the value of trajetId
        console.log('trajetId:', trajetId);

        if (trajetId) {
            fetchParticipants(trajetId);
        } else {
            alert('Trajet ID is undefined');
        }
        console.log("JavaScript file loaded");
    });

    $('#confirmParticipant').click(function() {
        displayRatingStars();
    });
});

