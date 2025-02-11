// Utility functions to get elements by ID and Class
let getById = (id, parent) => parent ? parent.getElementById(id) : getById(id, document);
let getByClass = (className, parent) => parent ? parent.getElementsByClassName(className) : getByClass(className, document);

// DOM elements mapped to variables for easier access
const DOM =  {
	chatListArea: getById("chat-list-area"),
	messageArea: getById("message-area"),
	inputArea: getById("input-area"),
	chatList: getById("chat-list"),
	messages: getById("messages"),
	chatListItem: getByClass("chat-list-item"),
	messageAreaName: getById("name", this.messageArea),
	messageAreaPic: getById("pic", this.messageArea),
	messageAreaNavbar: getById("navbar", this.messageArea),
	messageAreaDetails: getById("details", this.messageAreaNavbar),
	messageAreaOverlay: getByClass("overlay", this.messageArea)[0],
	messageInput: getById("input"),
	profileSettings: getById("profile-settings"),
	profilePic: getById("profile-pic"),
	profilePicInput: getById("profile-pic-input"),
	inputName: getById("input-name"),
	username: getById("username"),
	displayPic: getById("display-pic"),
};

// Class list utility for adding/removing classes from elements
let mClassList = (element) => {
	return {
		add: (className) => {
			element.classList.add(className);
			return mClassList(element);
		},
		remove: (className) => {
			element.classList.remove(className);
			return mClassList(element);
		},
		contains: (className, callback) => {
			if (element.classList.contains(className))
				callback(mClassList(element));
		}
	};
};

// State variables
let areaSwapped = false;
let chat = null;
let chatList = [];
let lastDate = "";
let selectedMediaFile = null; // Track the selected file for media messages

// Media preview elements
const mediaPreviewImage = document.getElementById('media-preview-image');
const mediaPreviewText = document.getElementById('media-preview-text');


// Function to trigger file input dialog when paperclip icon is clicked
function triggerFileInput() {
    document.getElementById('file-input').click();
}


// Handle file selection and prepare media preview
function prepareMediaMessage(event) {
    selectedMediaFile = event.target.files[0];
    displayMediaPreview(selectedMediaFile);
}

// Function to format time
function formatTime(utcTime) {
    const date = new Date(utcTime); // Interpret as UTC
    const localTime = new Date(date.toLocaleString("en-US", { timeZone: "Africa/Nairobi" })); // Convert to local timezone
    let hours = localTime.getHours();
    const minutes = localTime.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12; // Convert to 12-hour format
    return `${hours}:${minutes} ${ampm}`;
}

function enlargeImage(src) {
    // Create an overlay for image preview
    const overlay = document.createElement('div');
    overlay.className = 'enlarged-img-overlay';
    overlay.innerHTML = `
        <img src="${src}" alt="Enlarged Image">
        <div class="close-overlay" onclick="document.body.removeChild(this.parentElement)">×</div>
    `;
    document.body.appendChild(overlay);
}


// Function to display media preview before sending
function displayMediaPreview(file) {
    // Hide the text input and emoji icon
    DOM.messageInput.classList.add('d-none'); // Hide text input
    document.querySelector('.far.fa-smile').classList.add('d-none'); // Hide emoji icon  
    document.querySelector('.fas.fa-paper-plane').classList.add('d-none');
    DOM.inputArea.classList.add('d-none'); // Hide input area

    // Show the media preview container
    document.getElementById('media-preview-container').classList.remove('d-none');

    // Reset preview area
    mediaPreviewImage.classList.add('d-none');
    mediaPreviewText.classList.add('d-none');
    
    if (!file) return;

    // Show image preview
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            mediaPreviewImage.src = e.target.result;
            mediaPreviewImage.classList.remove('d-none');
            mediaPreviewText.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    }
    
    // Show video or PDF preview
    else if (file.type.startsWith('video/') || file.type === 'application/pdf') {
        mediaPreviewText.textContent = `File: ${file.name}`;
        mediaPreviewText.classList.remove('d-none');
        mediaPreviewImage.classList.add('d-none');
    }

    // Other media types (audio, document, etc.)
    else {
        mediaPreviewText.textContent = `File: ${file.name}`;
        mediaPreviewText.classList.remove('d-none');
        mediaPreviewImage.classList.add('d-none');
    }
}


// Function to cancel the media preview and restore the text input
function cancelMediaPreview() {
    // Restore the text input and emoji icon
    DOM.messageInput.classList.remove('d-none'); // Show text input
    document.querySelector('.far.fa-smile').classList.remove('d-none'); // Show emoji icon
    document.querySelector('.fas.fa-paper-plane').classList.remove('d-none');
    DOM.inputArea.classList.remove('d-none'); // Show input area

    // Hide the media preview container
    document.getElementById('media-preview-container').classList.add('d-none');
    // Reset the selected media file
    selectedMediaFile = null;
    clearMediaPreview();
}


// Functionality of Getting Messages asynchronously
let lastMessageId = 0; // Track last message ID
let messagesMap = {}; // Maps sender IDs to arrays of messages

// Fetch updates for new messages
function fetchUpdates() {
    fetch(`get_updates.php?last_message_id=${lastMessageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    // Update messagesMap for the sender
                    if (!messagesMap[msg.sender]) {
                        messagesMap[msg.sender] = [];
                    }
                    // Avoid duplicates by checking message ID
                    if (!messagesMap[msg.sender].some(m => m.id === msg.id)) {
                        messagesMap[msg.sender].push(msg);
                    }
                    // Update chat list
                    const existingChat = chatList.find(chat => chat.sender === msg.sender);
                    if (!existingChat) {
                        // Replace 'null' in lastMessage with a media type
                        function formatLastMessage(msg) {
                            if (msg.body) return msg.body;
                            if (msg.media_path) {
                                return "Media";
                            }
                            return "Unknown";
                        }
                        
                        chatList.push({
                            sender: msg.sender,
                            name: msg.name || msg.sender,
                            profilePhoto: msg.profile_photo || "https://via.placeholder.com/50",
                            // lastMessage: formatLastMessage(msg),
                            lastMessage: msg.body || `<i class="bi bi-file-earmark-text"></i> Media File`, // Fallback to "Media" if no text content
                            time: msg.time,
                        });

                    } else {
                        // Update existing chat
                        existingChat.name = msg.name || msg.sender;
                        existingChat.lastMessage = msg.body || `<i class="bi bi-file-earmark-text"></i> Media File`;
                        existingChat.time = msg.time;
                    }

                    // Update message area if the current chat is open
                    if (chat && chat.sender === msg.sender) {
                        addMessageToMessageArea(msg);
                    }

                    // Track the latest message ID
                    if (msg.id > lastMessageId) lastMessageId = msg.id;
                });

                // Refresh the chat list UI
                viewChatList();
            }
        })
        .catch(error => console.error("Error fetching updates:", error));
}

// Periodic polling every 1 second
setInterval(fetchUpdates, 1000);


// Send message (either text or media)
function sendMessage() {
    let messageContent = DOM.messageInput.value.trim();

    // If media file is selected, send it as a media message
    if (selectedMediaFile) {
        sendMediaMessage(selectedMediaFile);
        selectedMediaFile = null;  // Reset after sending
        clearMediaPreview();
    } else if (messageContent) {
        // Send text message
        let msgData = {
            sender: chat.sender,
            body: messageContent,
            direction: "outgoing"
        };

        // Call server endpoint to send the text message
        fetch('send_reply.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(msgData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                //console.log("Message sent: ", data);
                // Avoid adding a duplicate message to the UI
                if (!messagesMap[chat.sender]) messagesMap[chat.sender] = [];
                
                // Add only if `fetchUpdates` hasn't already added it
                if (!messagesMap[chat.sender].some(m => m.body === msgData.body && m.time === msgData.time)) {
                    messagesMap[chat.sender].push({ ...msgData, id: data.messageId, time: Date.now() });
                }


                //addMessageToMessageArea(msgData); // Show in UI
                DOM.messageInput.value = ""; // Clear input
                generateChatList();
            } else {
                console.error("Error sending text message:", data.error);
            }
        })
        .catch(error => console.error("Error:", error));
    }
}

// Send media message (e.g., image, PDF, or video)
function sendMediaMessage(file) {
    let formData = new FormData();
    formData.append('file', file);
    formData.append('recipient', chat.sender);

    const sendButton = document.getElementById('send-media');
    const cancelButton = document.getElementById('cancel-media');

    // Show spinner and disable buttons
    sendButton.disabled = true;
    cancelButton.disabled = true;
    
     // Clear preview and restore input area
    clearMediaPreview();
    restoreInputArea();
                
    // Call server endpoint to send the file
    fetch('send_reply.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                
                console.log("Media message sent successfully:", data);

                // Add the media message to the UI
                addMessageToMessageArea({
                    //body: file.name,
                    media_path: data.file_path, // Use returned file path
                    direction: "outgoing",
                    time: Date.now()
                });
                
            } else {
                console.error("Error sending media message:", data.error);
            }
        })
        .catch(error => console.error("Error:", error))
        .finally(() => {
            // Restore Send button and re-enable Cancel button
            sendButton.disabled = false;
            cancelButton.disabled = false;
        });
}


// Clear the media preview after sending
function clearMediaPreview() {
    mediaPreviewImage.src = '';
    mediaPreviewText.textContent = '';
    mediaPreviewImage.classList.add('d-none');
    mediaPreviewText.classList.add('d-none');

    // Hide the media-preview-container
    document.getElementById('media-preview-container').classList.add('d-none');
}

function restoreInputArea() {
    // Restore the text input and emoji icon
    DOM.messageInput.classList.remove('d-none'); // Show text input
    document.querySelector('.far.fa-smile').classList.remove('d-none'); // Show emoji icon
    document.querySelector('.fas.fa-paper-plane').classList.remove('d-none'); // Show send button
    DOM.inputArea.classList.remove('d-none'); // Show input area

    // Hide the media preview container
    document.getElementById('media-preview-container').classList.add('d-none');
}



// Populate chat list based on grouped messages by sender
function populateChatList() {
    let senderMap = {};

    chatList.forEach((chat, index) => {
        DOM.chatList.innerHTML += `
            <div class="chat-list-item d-flex flex-row w-100 p-2 border-bottom" onclick="openChat(${index})">
                <img src="${chat.profilePhoto}" alt="dp" class="img-fluid rounded-circle mr-2" style="height:50px;">
                <div class="w-50">
                    <div class="name">${chat.name || chat.sender}</div> <!-- Use name if available -->
                    <div class="small last-message">${chat.lastMessage}</div>
                </div>
                <div class="flex-grow-1 text-right">
                    <div class="small time">${mDate(chat.time).chatListFormat()}</div>
                </div>
            </div>`;
    });

    messages.forEach(msg => {
        const senderId = msg.sender;

        if (!senderMap[senderId]) {
            senderMap[senderId] = {
                sender: senderId,
                name: msg.name || senderId, // Use name or fallback
                // lastMessage: msg.body,
                lastMessage: msg.body || `<i class="bi bi-file-earmark-text"></i> Media File`,
                time: msg.time
            };
            chatList.push(senderMap[senderId]);
        } else {
            senderMap[senderId].lastMessage = msg.body;
            senderMap[senderId].time = msg.time;
        }
    });

    chatList = Object.values(senderMap).sort((a, b) => new Date(b.time) - new Date(a.time));
    viewChatList(); // Refresh the chat list
}


// Display each chat in the chat list area
function viewChatList() {
    DOM.chatList.innerHTML = "";  // Clear previous chats
    const placeholderImage = "images/was-dp.jpg"; // Placeholder image URL https://via.placeholder.com/50

    chatList.forEach((chat, index) => {
        const truncatedName = chat.name.length > 29 ? chat.name.substring(0, 26) + "..." : chat.name;
        DOM.chatList.innerHTML += `
            <div class="chat-list-item d-flex flex-row w-100 p-2 border-bottom" onclick="openChat(${index})">
                <img src="${placeholderImage}" alt="dp" class="img-fluid rounded-circle mr-2" style="height:50px;">
                <div class="w-50">
                    <div class="name">${truncatedName}</div>
                    <div class="small last-message">${chat.lastMessage}</div>
                </div>
                <div class="flex-grow-1 text-right">
                    <div class="small time">${mDate(chat.time).chatListFormat()}</div>
                </div>
            </div>`;
    });
        // Update chat list items reference
    DOM.chatListItem = getByClass("chat-list-item");
}


// Generate the chat list view
let generateChatList = () => {
	populateChatList();
	viewChatList();
};

// Add date header to message area when date changes
let addDateToMessageArea = (date) => {
	DOM.messages.innerHTML += `
	<div class="mx-auto my-2 bg-primary text-white small py-1 px-2 rounded">
		${date}
	</div>
	`;
};

// Add individual message to message area
function addMessageToMessageArea(msg) {
    let mediaHTML = "";
    if (msg.media_path) {
        const mediaPath = msg.media_path;

        // Image handling
        if (mediaPath.match(/\.(jpg|jpeg|png|gif)$/i)) {
            mediaHTML = `<img src="${mediaPath}" alt="image" class="media-img" onclick="enlargeImage('${mediaPath}')">`;
            //Audio Handling
        } else if (mediaPath.match(/\.(mp3|wav|ogg)$/i)) {
            mediaHTML = `<audio controls class="media-audio"><source src="${mediaPath}" type="audio/mpeg">Your browser does not support the audio element.</audio>`;

        // Video handling
        } else if (mediaPath.match(/\.(mp4)$/i)) {
            mediaHTML = `<video controls class="media-video"><source src="${mediaPath}" type="video/mp4">Your browser does not support the video element.</video>`;
        
        // PDF file handling
        } else if (mediaPath.match(/\.(pdf)$/i)) {
            mediaHTML = `
                <div class="document-preview">
                    <iframe src="${mediaPath}" width="100%" height="300px" class="document-preview-iframe"></iframe>
                    <div class="document-actions">
                        <a href="${mediaPath}" target="_blank" class="document-download">Download PDF</a> |
                        <a href="javascript:void(0);" class="document-print" onclick="window.print();">Print</a>
                    </div>
                </div>`;

        // Office Documents (.doc, .docx, .xls, .xlsx, .ppt, .pptx)
        } else if (mediaPath.match(/\.(doc|docx|xls|xlsx|ppt|pptx)$/i)) {
            mediaHTML = `
                <div class="document-preview">
                    <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(mediaPath)}" 
                            width="100%" height="300px" class="document-preview-iframe">
                    </iframe>
                    <div class="document-actions">
                        <a href="${mediaPath}" target="_blank" class="document-download">Download Document</a> |
                        <a href="javascript:void(0);" class="document-print" onclick="window.print();">Print</a>
                    </div>
                </div>`;

        // For unsupported file types, provide a download link
        } else {
            mediaHTML = `<span>File type not supported for preview. <a href="${mediaPath}" target="_blank">Download</a></span>`;
        }
    }

    // Existing message addition with dynamic date and media content
    const currentDate = new Date(msg.time || Date.now()); // Use message time or current time
    const messageTime = currentDate.toLocaleTimeString(); // Format time
    const formattedDate = currentDate.toDateString();

    // Add a date header if the date changes
    if (!DOM.messages.lastDate || DOM.messages.lastDate !== formattedDate) {
        DOM.messages.innerHTML += `
        <div class="mx-auto my-2 bg-primary text-white small py-1 px-2 rounded">
            ${formattedDate}
        </div>`;
        DOM.messages.lastDate = formattedDate; // Track the last displayed date
    }

    // Append the message to the message area
    DOM.messages.innerHTML += `
    <div class="align-self-${msg.direction === "outgoing" ? "end self" : "start"} p-1 my-1 mx-3 rounded bg-white shadow-sm message-item" data-message-id="${msg.id}">
        <div class="options dropdown">
            <a class=" dropdown-toggle"  href="#" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><i class="fas fa-angle-down text-muted px-2"></i></a>
            <div class="dropdown-menu dropdown-menu-right mt-3">
                <a class="dropdown-item" href="#" onclick="showDeleteModal('${msg.id}')"><i class="bi bi-trash3 mx-2"></i>Delete</a>
            </div>
        </div>
        <div class="">
            <div class="body m-1 mr-2">${msg.body || ""}</div>
            ${mediaHTML}  <!-- Media content displayed here -->
            <div class="time small text-muted" style="margin-top: 5px;">${formatTime(msg.time)}</div>
        </div>
    </div>`;

    // Scroll to the latest message
    DOM.messages.scrollTo(0, DOM.messages.scrollHeight);
}


// Open chat in the message area when clicked in the chat list
let openChat = (chatIndex) => {
	chat = chatList[chatIndex];
	
    
	mClassList(DOM.inputArea).contains("d-none", (elem) => elem.remove("d-none").add("d-flex"));
	mClassList(DOM.messageAreaOverlay).add("d-none");

	if (window.innerWidth <= 575) {
        mClassList(DOM.chatListArea).remove("d-flex").add("d-none");
        mClassList(DOM.messageArea).remove("d-none").add("d-flex");
        areaSwapped = true;
    } else {
        [...DOM.chatListItem].forEach((elem) => mClassList(elem).remove("active"));
        mClassList(DOM.chatListItem[chatIndex]).add("active");
    }

    
	// Set chat details
	DOM.messageAreaName.innerHTML = chat.name;
	document.getElementById("dropdownUsername").innerHTML = chat.name;
	document.getElementById("dropdownStatus").innerHTML = `~${chat.name}`;
	document.getElementById("dropdownNumber").innerHTML = `+${chat.sender}`;
	DOM.messages.innerHTML = "";
	lastDate = "";

        // Refresh messages from `messagesMap` or fallback to the existing array
    const chatMessages = messagesMap[chat.sender] || messages;
	// Filter and display messages for the selected chat
	chatMessages
		.filter(msg => msg.sender === chat.sender)
		.sort((a, b) => mDate(a.time).subtract(b.time))
		.forEach((msg) => addMessageToMessageArea(msg));
	
};

// Show the chat list in mobile view when returning from a specific chat
let showChatList = () => {
	if (areaSwapped) {
		mClassList(DOM.chatListArea).remove("d-none").add("d-flex");
		mClassList(DOM.messageArea).remove("d-flex").add("d-none");
		areaSwapped = false;
	}
};


let messageToDelete = null; // Track the message to delete
function showDeleteModal(messageId) {
    messageToDelete = messageId; // Store the message ID
    $('#deleteModal').modal('show'); // Show the modal using Bootstrap's modal plugin
}

function deleteMessageForMe() {
    fetch('delete_me.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ messageId: messageToDelete })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the message from the UI
            const messageElement = document.querySelector(`div[data-message-id="${messageToDelete}"]`);
            if (messageElement) {
                messageElement.remove();
            }
        } else {
            alert("Failed to delete the message for me.");
        }
    })
    .catch(error => console.error("Error deleting message:", error))
    .finally(() => {
        // Close the modal
        $('#deleteModal').modal('hide');
    });
}

// Function to save the custom name
function saveCustomName() {
    const nameInput = document.getElementById('updateCustomName');
    const customName = nameInput.value.trim();

    if (!customName) {
        alert('Please enter a custom name before saving.');
        return;
    }

    // Assume chat.sender holds the current chat's phone number
    if (!chat || !chat.sender) {
        alert('No chat selected. Please select a chat to update.');
        return;
    }

    const phone = normalizePhoneNumber(chat.sender);

    // Send request to the backend
    fetch('update_name.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name: customName, phone: phone }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the dropdownUsername in the UI
                document.getElementById('dropdownUsername').textContent = customName;
                alert('Name updated successfully! Refresh To Update.');
            } else {
                alert('Failed to update name: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error updating name:', error);
            alert('An error occurred while updating the name.');
        });
}

// Function to normalize phone numbers
function normalizePhoneNumber(phone) {
    // Remove non-numeric characters
    let normalizedPhone = phone.replace(/\D/g, '');
    
    if (normalizedPhone.length === 10) {
        normalizedPhone = '254' + normalizedPhone.substring(1); // Add country code
    } else if (normalizedPhone.length > 10 && normalizedPhone.startsWith('0')) {
        normalizedPhone = '254' + normalizedPhone.substring(1); // Replace leading 0 with country code
    }

    return normalizedPhone;
}

// Attach the function to the button click
document.querySelector('.save-name').addEventListener('click', saveCustomName);



// Profile settings functions
let showProfileSettings = () => {
	DOM.profileSettings.style.left = 0;
	DOM.profilePic.src = user.pic;
	DOM.inputName.value = user.name;
};

let hideProfileSettings = () => {
	DOM.profileSettings.style.left = "-110%";
	DOM.username.innerHTML = user.name;
};


// Contact Settings
function showContactSettings() {
    document.getElementById("contact-settings").style.left = "0";
}

function hideContactSettings() {
    document.getElementById("contact-settings").style.left = "-110%";
}


// Window resize listener for responsive chat list display
window.addEventListener("resize", () => {
	if (window.innerWidth > 575) showChatList();
});

// Initialize the application with user data and chat list
let init = () => {
	DOM.username.innerHTML = user.name;
	DOM.displayPic.src = user.pic;
	DOM.profilePic.src = user.pic;
	DOM.profilePic.addEventListener("click", () => DOM.profilePicInput.click());
	DOM.profilePicInput.addEventListener("change", () => console.log(DOM.profilePicInput.files[0]));
	DOM.inputName.addEventListener("blur", (e) => user.name = e.target.value);
	generateChatList();

// 	console.log("Click the Image at top-left to open settings.");
};

init();
