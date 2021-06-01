let messages = document.querySelector('#chat-content');
let room = document.querySelector('#user-room').value;
let user = document.querySelector('#room-user').dataset.user;

setInterval(() => {
    // axios.get(Routing.generate('get_rest_new_messages'), {
    //     params: {
    //         room: room,
    //         time: new Date(),
    //     }
    // })
    //     .then(function (response) {
    //        console.log(response)
    //     });

    //alert(new Date());

    // let templateNode = document.querySelector('#message-template').cloneNode(true);
    // templateNode.querySelector('h1').innerText = data.message;
    // templateNode.style.display = 'block';
    // messages.appendChild(templateNode);
}, 1000);