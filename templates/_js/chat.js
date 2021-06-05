let messages = document.querySelector('#chat-content');
let room = document.querySelector('#user-room').value;
let user = document.querySelector('#room-user').dataset.user;
let items = document.querySelectorAll(".media-chat");
let last = items[items.length-1];
last.scrollIntoView();

let newMessages = [];

setInterval(() => {
    axios.get(Routing.generate('get_rest_new_messages'), {
        params: {
            room: room,
            time: new Date(),
        }
    })
        .then(function (response) {
            newMessages = [];
            if (response.data.length > 0) {
                for (let i = 0; i < response.data.length; i++) {
                    newMessages.push(JSON.parse(JSON.stringify(response.data[i])));
                }

            }
        })

    if (newMessages.length > 0) {
        let templateMessageNode = document.querySelector('#chat-template-message').cloneNode(true);
        let templateMessageOwnerNode = document.querySelector('#chat-template-owner').cloneNode(true);
        for (let n = 0; n < newMessages.length; n++) {
            let message = document.getElementById(newMessages[n].id);
            if (message === null) {
                if (parseInt(newMessages[n].userId) === parseInt(user)) {
                    templateMessageOwnerNode.id = newMessages[n].id;
                    templateMessageOwnerNode.querySelector('p:first-child').innerText = newMessages[n].content;
                    templateMessageOwnerNode.querySelector('p:last-child').innerText = newMessages[n].time;
                    templateMessageOwnerNode.classList.remove('d-none');
                    messages.appendChild(templateMessageOwnerNode);
                } else {
                    templateMessageNode.id = newMessages[n].id;
                    templateMessageNode.querySelector('p:first-child').innerText = newMessages[n].userName;
                    templateMessageNode.querySelector('p:nth-child(2)').innerText = newMessages[n].content;
                    templateMessageNode.querySelector('p:last-child').innerText = newMessages[n].time;
                    templateMessageNode.classList.remove('d-none');
                    messages.appendChild(templateMessageNode);
                }
            }
        }
        let updateItems = document.querySelectorAll(".media-chat");
        let lastMessage = updateItems[updateItems.length-1];
        lastMessage.scrollIntoView();
    }
}, 1000);