function fetchParticipants(trajetId) {
    $.get(`/note_participants/${trajetId}`)
        .done(data => displayParticipants(data))
        .fail((jqXHR, textStatus, errorThrown) => {
            console.error('Error fetching participants:', textStatus, errorThrown);
        });
}

function displayParticipants(participants) {
    const participantsList = $('#selectParticipantsList');

    if (participantsList.length) {
        participantsList.empty();

        participants.forEach(participant => {
            const optionItem = $('<option>', {
                value: participant.id,
                text: `${participant.prenom} ${participant.nom}`
            });

            if (participant.selected) {
                optionItem.prop('selected', true);
            }

            participantsList.append(optionItem);
        });

        participantsList.show();
        $('#confirmParticipant').show();
    } else {
        console.error('selectParticipantsList element not found');
    }
}

function displayRatingStars() {
    const ratingContainer = $('#ratingContainer');
    const ratingStars = $('#ratingStars');

    if (ratingContainer.length && ratingStars.length) {
        ratingStars.empty();

        for (let i = 1; i <= 5; i++) {
            const star = $('<input>', {
                type: 'radio',
                name: 'rating',
                value: i,
                id: `star${i}`,
                class: 'rating-star'
            });

            const starLabel = $('<label>', {
                for: `star${i}`,
                text: '★',
                class: 'rating-label'
            });

            ratingStars.append(star);
            ratingStars.append(starLabel);
        }

        ratingContainer.show();
        $('#confirmRating').show();
    } else {
        console.error('ratingContainer or ratingStars element not found');
    }
}

function submitRating() {
    const trajetId = $(':input.trajet-id').val();
    const participantId = $('#selectParticipantsList').val();
    const note = $('#ratingContainer input[name="rating"]:checked').val();

    console.log('trajetId:', trajetId);
    console.log('participantId:', participantId);
    console.log('note:', note);

    const debugElement = $('#debug');
    debugElement.text(`trajetId: ${trajetId}, participantId: ${participantId}, note: ${note}`);

    $.ajax({
        url: '/save_rating',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            trajet_id: trajetId,
            participant_id: participantId,
            note: note
        })
    })
        .done(data => {
            if (data.success) {
                alert('Rating saved successfully');
                $('#ratingContainer input[name="rating"]').prop('checked', false);
                $('#ratingContainer textarea').val('');
                $('#ratingContainer').hide();
            } else {
                alert('Error saving rating');
            }
        })
        .fail((jqXHR, textStatus, errorThrown) => {
            if (jqXHR.status === 409) { // 409 Conflict - When the rating already exists
                alert('You have already submitted a rating for this participant.');
            } else {
                console.error('Error:', textStatus, errorThrown);
            }
        });
}

$(document).ready(function() {
    $('#form_trajet').submit(function(event) {
        event.preventDefault();
        const trajetId = $(':input.trajet-id').val();

        console.log('trajetId:', trajetId);

        if (trajetId) {
            fetchParticipants(trajetId);
        } else {
            alert('Trajet ID is undefined');
        }
    });

    $('#confirmParticipant').click(function() {
        displayRatingStars();
    });

    $('#confirmRating').click(function(event) {
        event.preventDefault();
        submitRating();
    });
});
