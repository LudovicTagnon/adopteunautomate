$(document).ready(function() {
    $('#form_trajet').submit(function(event) {
        event.preventDefault();
        var trajetId = $('#trajet-select').val();
        $.ajax({
            type: "GET",
            url: "/note_participants",
            data: {
                'id': trajetId
            },
            success: function(data) {
                $('#participants-container').html(data);
            },
            error: function(xhr, status, error) {
                alert('Une erreur est survenue : ' + status);
            }
        });
    });
});
