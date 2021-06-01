let form = document.getElementById('send-message');

form.onsubmit = function (event) {
    event.preventDefault();
    axios({
        method: 'PUT',
        url: Routing.generate('put_rest_message'),
        data: {
            message: document.getElementById('user-message').value,
            room: document.getElementById('user-room').value
        }
    })
}