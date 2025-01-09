<?php
require 'config.php';

// Get the database connection
$conn = getDatabaseConnection();

/**
 * Function to get the selected chat
 */
function getSelectedChat($chats) {
    $selectedChat = $_GET['sender_id'] ?? null;

    // If no sender_id provided, return null
    if (!$selectedChat) {
        return null;
    }

    // Validate if the sender_id exists in the chat list
    while ($chat = $chats->fetch_assoc()) {
        if ($chat['sender_id'] === $selectedChat) {
            return $selectedChat;
        }
    }

    // If no match found, return null
    return null;
}

// Fetch chats with the last message for sorting
$chatsQuery = "
    SELECT sender_id, ig_id, MAX(timestamp) as last_time, 
           (SELECT message_text FROM instagram_messages 
            WHERE sender_id = im.sender_id 
            ORDER BY timestamp DESC LIMIT 1) as last_message 
    FROM instagram_messages im 
    GROUP BY sender_id 
    ORDER BY last_time DESC
";
$chats = $conn->query($chatsQuery);

// Get the selected chat
$selectedChat = getSelectedChat($chats);

// Fetch messages only if a chat is selected
$messages = [];
if ($selectedChat) {
    $messagesQuery = "SELECT * FROM instagram_messages WHERE sender_id = ? ORDER BY timestamp ASC";
    $stmt = $conn->prepare($messagesQuery);
    $stmt->bind_param("s", $selectedChat);
    $stmt->execute();
    $messages = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="https://unpkg.com/tailwindcss@1.4.6/dist/tailwind.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="insta.png" type="image/x-icon">
    <style>
        /* can be configured in tailwind.config.js */
        .group:hover .group-hover\:block {
            display: block;
        }
	.hover\:w-64:hover {
    	    width: 45%;
    	}
	/* NO NEED THIS CSS - just for custom scrollbar which can also be configured in tailwind.config.js*/
	::-webkit-scrollbar {
	    width: 2px;
	    height: 2px;
	}
	::-webkit-scrollbar-button {
	    width: 0px;
	    height: 0px;
	}
	::-webkit-scrollbar-thumb {
	    background: #2d3748;
	    border: 0px none #ffffff;
	    border-radius: 50px;
	}
	::-webkit-scrollbar-thumb:hover {
	    background: #2b6cb0;
	}
	::-webkit-scrollbar-thumb:active {
	    background: #000000;
	}
	::-webkit-scrollbar-track {
	    background: #1a202c;
	    border: 0px none #ffffff;
	    border-radius: 50px;
	}
	::-webkit-scrollbar-track:hover {
	    background: #666666;
	}
	::-webkit-scrollbar-track:active {
	    background: #333333;
	}
	::-webkit-scrollbar-corner {
	    background: transparent;
	}
	
.media-preview {
    display: none;
    text-align: center; 
    padding:28px; 
    flex-direction: column; 
    margin-top: 5px; 
    margin-top: 5px;
    width: 560px;
    height: 400px;
    border-radius: 8px;
    margin-left: 10px;
    
}

.media-preview img, .media-preview video, .media-preview audio {
    width: 460px; 
    border-radius: 5px;
    margin-top: 5px;
    margin-bottom: 5px; 
    
}
.media-buttons {
    margin-left: 10px;
    margin-bottom: 10px;
    position: absolute;
    display: flex;
    justify-content: center;
    gap: 18rem; /* Adds spacing between buttons */
    padding: 0.5rem;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
}
.media-buttons button {
  padding: 10px 20px;
  border-radius: 5px;
  border: none;
  font-size: 20px;
  cursor: pointer;
}

.media-buttons .btn-cancel {
  background-color: #fff;
  color: #000;
}

.media-buttons .btn-send {
  background-color: #1b8755;
  color: #fff;
}


.messages img, 
.messages video, 
.messages audio {
    width: 300px;
    border-radius: 10px;
    margin-top: 10px;
    max-height: 250px;
    padding: 8px; /* Add padding */
    background-color: #f0f0f0; /* Set the background color for padding */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Optional: Add shadow for a better look */
}

/* Button Styling */
    .instagram-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px; /* Adjust button size */
        height: 38px;
        border: none;
        border-radius: 50%; /* Circle Shape */
        background: linear-gradient(45deg, #FEDA77, #F58529, #DD2A7B, #8134AF, #515BD4);
        color: white;
        font-size: 20px; /* Adjust icon size */
        text-decoration: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .instagram-button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }
    
    /* Icon Styling - Font Awesome */
    .instagram-button i {
        display: inline-block;
    }
/* Spinner styles */
.spinner {
  border: 2px solid #f3f3f3; /* Light gray */
  border-top: 2px solid #1b8755; /* Green */
  border-radius: 50%;
  width: 16px;
  height: 16px;
  animation: spin 0.8s linear infinite;
  display: inline-block;
  vertical-align: middle;
  margin-left: 8px; /* Space between text and spinner */
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Disable button styles */
.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.chat-body {
    overflow-y: auto; /* Ensures scrolling works */
    height: 100%; /* Makes sure it fills the container */
}

#chat-area {
    overflow-y: auto;
    height: 100%;
}

    </style>
</head>
<body>

<div class="h-screen w-full flex antialiased text-gray-800 bg-white overflow-hidden">
    <div class="flex-1 flex flex-col">
        <main class="flex-grow flex flex-row min-h-0">
            <section class="flex flex-col flex-none overflow-auto w-24 lg:max-w-sm md:w-2/5 transition-all duration-300 ease-in-out">
                <div class="header p-4 flex flex-row justify-between items-center flex-none" style="background: #f4f5f8;">
                    <div class="w-16 h-16 relative flex flex-shrink-0">
                        <img class="rounded-full w-full h-full object-cover" alt="ravisankarchinnam"
                             src="insta.png"/>
                    </div>
                    <p class="text-md font-bold hidden md:block"> <img class=" w-full h-full object-cover" alt="ravisankarchinnam"
                             src="media/in-icon.png"/></p>
                    <a href="#" class="block rounded-full hover:bg-gray-200 bg-gray-100 w-10 h-10 p-2">
                        <svg viewBox="0 0 24 24" class="w-full h-full">
                            <path
                                    d="M6.3 12.3l10-10a1 1 0 0 1 1.4 0l4 4a1 1 0 0 1 0 1.4l-10 10a1 1 0 0 1-.7.3H7a1 1 0 0 1-1-1v-4a1 1 0 0 1 .3-.7zM8 16h2.59l9-9L17 4.41l-9 9V16zm10-2a1 1 0 0 1 2 0v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6c0-1.1.9-2 2-2h6a1 1 0 0 1 0 2H4v14h14v-6z"/>
                        </svg>
                    </a>
                </div>
                <div class="search-box p-4 flex-none">
                    <form onsubmit="">
                        <div class="relative">
                            <label>
                                <input class="rounded-full py-2 pr-6 pl-10 w-full border border-gray-200 bg-gray-200 focus:bg-white focus:outline-none text-gray-600 focus:shadow-md transition duration-300 ease-in"
                                       type="text" value="" placeholder="Search Instagram"/>
                                <span class="absolute top-0 left-0 mt-2 ml-3 inline-block">
                                    <svg viewBox="0 0 24 24" class="w-6 h-6">
                                        <path fill="#bbb"
                                              d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"/>
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </form>
                </div>
                <div class="contacts p-2 flex-1 overflow-y-scroll">
                    <?php while ($chat = $chats->fetch_assoc()): 
                        $timestamp = strtotime($chat['last_time']);
                        $now = time();
                        // Format the timestamp dynamically
                        if (date('Y-m-d', $timestamp) === date('Y-m-d', $now)) {
                            // Show time if today
                            $formattedTime = date('g:i A', $timestamp);
                        } else {
                            // Show date for older messages
                            $formattedTime = date('m/d/Y', $timestamp);
                        }
                    ?>
                        <div class="flex justify-between items-center p-3 hover:bg-gray-100 rounded-lg relative <?= $chat['sender_id'] === $selectedChat ? 'active-chat' : '' ?>" 
                             data-sender-id="<?= htmlspecialchars($chat['sender_id']) ?>" 
                             data-ig-id="<?= htmlspecialchars($chat['ig_id']) ?>" 
                             onclick="selectChat(this)" 
                             style="cursor: pointer;">
                            
                            <!-- Profile Photo -->
                            <div class="w-16 h-16 relative flex flex-shrink-0">
                                <img class="shadow-md rounded-full w-full h-full object-cover"
                                     src="was-dp.jpg" 
                                     alt="Profile Photo"
                                />
                            </div>
                            <!-- Sender Details -->
                            <div class="flex-auto min-w-0 ml-4 mr-6 hidden md:block">
                                <!-- Dynamic Sender ID -->
                                <p><?= htmlspecialchars($chat['sender_id']) ?></p>
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <div class="min-w-0">
                                        <p class="truncate"><?= htmlspecialchars($chat['last_message'] ?: 'Media File') ?></p>
                                    </div>
                                    <p class="ml-2 whitespace-no-wrap"><?= $formattedTime ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>

            <section class="flex flex-col flex-auto border-l">
                <div class="chat-header px-6 py-4 flex flex-row flex-none justify-between items-center shadow hidden" id="chatNav">
                    <div class="flex">
                        <div class="w-12 h-12 mr-4 relative flex flex-shrink-0">
                            <img class="shadow-md rounded-full w-full h-full object-cover"
                                 src="was-dp.jpg"
                                 alt=""
                            />
                        </div>
                        <div class="mt-4">
                            <p class="font-bold" id="chatHeader"></p>
                        </div>
                    </div>
                    
                    <div class="flex">
                        <div class="relative inline-block text-left">
                        <button id="options-menu" class="instagram-button block rounded-full  w-10 h-10 p-2 ml-4">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="dropdown-menu" class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                            <div class="p-4">
                                <label for="custom-name-input" class="block text-sm font-medium text-gray-700">Give a Custom Name</label>
                                <input type="text" id="custom-name-input" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none sm:text-sm" placeholder="Enter name">
                                <button id="save-custom-name" class="mt-2 w-full bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                
                <!-- Chat Body -->
                
                <div class="chat-body p-4 flex-1 overflow-y-scroll" >
                     <div class="messages" id="chat-area">
                        <!-- Messages will be dynamically loaded here #fafafa; -->
                    </div>

                </div>
                <!-- Media Preview -->
                <div class="media-preview " id="mediaPreview" style="background: #f0ecec">
                    <img id="previewImage" src="" alt="Selected Media" style="display: none;">
                    <video id="previewVideo" controls style="display: none;">
                        <source id="previewVideoSource" src="" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <audio id="previewAudio" controls style="display: none;">
                        <source id="previewAudioSource" src="" type="audio/mp3">
                        Your browser does not support the audio tag.
                    </audio>
                    <div class="media-buttons bottom-0">
                        <button class="btn btn-cancel" id="cancelMedia" onclick="cancelMedia()"><i class="bi bi-x-circle"></i></button>
                        <button class="btn btn-send" id="sendMedia" onclick="sendMedia()">
                            <i class="bi bi-send"></i>
                            <span class="spinner" id="sendSpinner" style="display: none;"></span>
                        </button>
                    </div>
                </div>
                
                <!--Chat Footer-->
                
                <div class="chat-footer flex-none hidden" id="chatFooter" >
                    <form id="replyForm" class="flex flex-row items-center p-4 relative" enctype="multipart/form-data">
                        <!-- Hidden Inputs for Form -->
                        <input type="hidden" name="sender_id" id="selectedSenderId" value="<?= htmlspecialchars($selectedChat) ?>">
                        <input type="hidden" name="ig_id" value="<?= htmlspecialchars($firstChat['ig_id'] ?? '') ?>">
                        <input type="file" id="mediaInput" style="display: none;" onchange="previewMedia(event)">
               
                        <!-- Media Button -->
                        <button type="button" class=" instagram-button flex flex-shrink-0 focus:outline-none  mx-2 block "
                                onclick="document.getElementById('mediaInput').click()">
                            <svg viewBox="0 0 20 20" width="26" height="26" fill="currentColor">
                                <path d="M11,13 L8,10 L2,16 L11,16 L18,16 L13,11 L11,13 Z M0,3.99406028 C0,2.8927712 0.898212381,2 1.99079514,2 L18.0092049,2 C19.1086907,2 20,2.89451376 20,3.99406028 L20,16.0059397 C20,17.1072288 19.1017876,18 18.0092049,18 L1.99079514,18 C0.891309342,18 0,17.1054862 0,16.0059397 L0,3.99406028 Z M15,9 C16.1045695,9 17,8.1045695 17,7 C17,5.8954305 16.1045695,5 15,5 C13.8954305,5 13,5.8954305 13,7 C13,8.1045695 13.8954305,9 15,9 Z"/>
                            </svg>
                        </button>
                
                        <!-- Placeholder Second Button -->
                        <!--<button type="button" class="instagram-button flex flex-shrink-0 focus:outline-none mx-2 block text-blue-600 hover:text-blue-700 w-6 h-6">-->
                        <!--    <svg viewBox="0 0 20 20" class="w-full h-full fill-current">-->
                        <!--        <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM6.5 9a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm7 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm2.16 3a6 6 0 0 1-11.32 0h11.32z"/>-->
                        <!--    </svg>-->
                        <!--</button>-->
                
                        <!-- Input Field for Messages -->
                        <div class="relative flex-grow">
                            <label style="display: flex;">
                                <input name="reply_message" id="replyMessage"
                                       class="rounded-full py-2 pl-3 mr-5 pr-10 w-full border border-gray-200 bg-gray-200 focus:bg-white focus:outline-none text-gray-600 focus:shadow-md transition duration-300 ease-in"
                                       type="text" placeholder="Type Message..." required
                                       />
                
                                <!-- Send Button -->
                                <button type="button"
                                    onclick="sendReply()"
                                    class="instagram-button top-0 right-0 mt-1  mr-5 flex flex-shrink-0 focus:outline-none block ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-send"
                                         viewBox="0 0 16 16">
                                        <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                                    </svg>
                                </button>
                            </label>
                        </div>
                    </form>
                </div>
                
            </section>
        </main>
    </div>
</div>


<script>
        document.addEventListener('DOMContentLoaded', () => {
            fetchChatListRealTime(); // Start updating chat list in real-time
            const optionsMenu = document.getElementById("options-menu");
            const dropdownMenu = document.getElementById("dropdown-menu");
            const saveCustomNameButton = document.getElementById("save-custom-name");
            const customNameInput = document.getElementById("custom-name-input");
            const chatHeader = document.getElementById("chatHeader");
            const selectedSenderIdInput = document.getElementById("selectedSenderId");
        
            // Toggle dropdown visibility
            optionsMenu.addEventListener("click", () => {
                dropdownMenu.classList.toggle("hidden");
            });
        
            // Save custom name functionality
            saveCustomNameButton.addEventListener("click", () => {
                const newCustomName = customNameInput.value.trim();
                const senderId = selectedSenderIdInput.value;
        
                if (!newCustomName || !senderId) {
                    alert("Please enter a valid name.");
                    return;
                }
        
                // Send AJAX request to update the sender's name
                fetch("update_sender_name.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `sender_id=${senderId}&custom_name=${encodeURIComponent(newCustomName)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Name updated successfully!");
                        chatHeader.textContent = newCustomName; // Update header with new name
                        dropdownMenu.classList.add("hidden");  // Hide dropdown
                        customNameInput.value = "";           // Clear input
                        fetchChatListRealTime();              // Refresh chat list
                    } else {
                        alert("Failed to update name: " + data.error);
                    }
                })
                .catch(error => {
                    console.error("Error updating name:", error);
                });
            });
        });

        function previewMedia(event) {
            const file = event.target.files[0];
            if (file) {
                const previewImage = document.getElementById('previewImage');
                const previewVideo = document.getElementById('previewVideo');
                const previewAudio = document.getElementById('previewAudio');
                const previewVideoSource = document.getElementById('previewVideoSource');
                const previewAudioSource = document.getElementById('previewAudioSource');

                // Hide all preview elements first
                previewImage.style.display = 'none';
                previewVideo.style.display = 'none';
                previewAudio.style.display = 'none';

                // Display the appropriate preview based on file type
                const fileExtension = file.name.split('.').pop().toLowerCase();
                if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
                    previewImage.src = URL.createObjectURL(file);
                    previewImage.style.display = 'block';
                } else if (fileExtension === 'mp4') {
                    previewVideoSource.src = URL.createObjectURL(file);
                    previewVideo.style.display = 'block';
                } else if (['mp3', 'wav'].includes(fileExtension)) {
                    previewAudioSource.src = URL.createObjectURL(file);
                    previewAudio.style.display = 'block';
                }

                document.getElementById('mediaPreview').style.display = 'flex';
                document.getElementById('replyForm').style.display = 'none';
            }
        }

        function cancelMedia() {
            document.getElementById('mediaInput').value = '';
            document.getElementById('previewImage').src = '';
            document.getElementById('previewVideo').style.display = 'none';
            document.getElementById('previewAudio').style.display = 'none';
            document.getElementById('mediaPreview').style.display = 'none';
            document.getElementById('replyForm').style.display = 'flex';
        }

        async function sendMedia() {
            const sendButton = document.getElementById('sendMedia');
            const cancelButton = document.getElementById('cancelMedia');
            const spinner = document.getElementById('sendSpinner');
        
            // Disable buttons and show spinner
            sendButton.disabled = true;
            cancelButton.disabled = true;
            spinner.style.display = 'inline-block'; // Show spinner
        
            try {
                const formData = new FormData();
                formData.append('ig_id', document.querySelector('[name="ig_id"]').value);
                formData.append('sender_id', document.querySelector('[name="sender_id"]').value);
                formData.append('media', document.getElementById('mediaInput').files[0]);
        
                const response = await fetch('send_media.php', {
                    method: 'POST',
                    body: formData,
                });
        
                if (response.ok) {
                    //alert('Media sent successfully!');
                    cancelMedia();
                } else {
                    alert('Failed to send media!');
                }
            } catch (error) {
                alert('An error occurred while sending media!');
                console.error(error);
            } finally {
                // Revert buttons and hide spinner
                sendButton.disabled = false;
                cancelButton.disabled = false;
                spinner.style.display = 'none'; // Hide spinner
            }
        }
        
        // Fetch messages in real-time
        let fetchInterval;
        
        function fetchMessagesRealTime(senderId) {
            clearInterval(fetchInterval); // Clear any previous interval
        
            fetchInterval = setInterval(() => {
                fetch(`fetch_messages.php?sender_id=${senderId}`)
                    .then(response => response.text())
                    .then(messagesHtml => {
                        const chatBody = document.querySelector('.chat-body .messages');
        
                        // Compare new messages with the existing content
                        if (chatBody.dataset.previousHtml !== messagesHtml) {
                            // Update chat content only if changes are detected
                            chatBody.innerHTML = messagesHtml;
                            chatBody.dataset.previousHtml = messagesHtml; // Store the current state
                        }
                    })
                    .catch(error => console.error('Error fetching messages:', error));
            }, 1000); // Poll every second
        }

        
        // Fetch and update the chat list in real-time
        function fetchChatListRealTime() {
            setInterval(() => {
                fetch('fetch_chats.php')
                    .then(response => response.text())
                    .then(chatListHtml => {
                        const chatListContainer = document.querySelector('.contacts'); // Chat list container
                        if (chatListContainer) {
                            chatListContainer.innerHTML = chatListHtml; // Update chat list content
                        }
                    })
                    .catch(error => console.error('Error fetching chat list:', error));
            }, 1000); // Fetch every 1 seconds
        }


        async function sendReply() {
            const sendButton = document.querySelector('.instagram-button'); // Select the send button
            sendButton.disabled = true;// Prevent double submission
            
            const replyMessage = document.getElementById('replyMessage').value.trim();
            const senderId = document.getElementById('selectedSenderId').value;
            const igId = document.querySelector('[name="ig_id"]').value;
            
            if (!replyMessage) {
                alert('Please enter a message.');
                sendButton.disabled = false; // Re-enable button if validation fails
                return;
            }
        
            if (!senderId) {
                alert('No chat selected. Please select a chat first.');
                sendButton.disabled = false; // Re-enable button if validation fails
                return;
            }
        
            const formData = new FormData();
            formData.append('sender_id', senderId);
            formData.append('reply_message', replyMessage);
            formData.append('ig_id', igId);
        
            try {
                const response = await fetch('reply.php', {
                    method: 'POST',
                    body: formData,
                });
        
                const result = await response.json();
        
                if (result.success) {
                    document.getElementById('replyMessage').value = '';// Clear the input field
                    fetchMessagesRealTime(senderId); // Immediately refresh messages
                } else {
                    alert(`Failed to send message: ${result.error || 'Unknown error'}`);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('An unexpected error occurred. Please try again.');
            }
        }
        
    // Initialize chat selection
    function selectChat(chatElement) {
        const chatBody = document.querySelector('.chat-body .messages');
        const chatArea = document.getElementById("chat-area");
        const footer = document.getElementById('chatFooter');
        const senderId = chatElement.dataset.senderId;
        const igId = chatElement.dataset.igId;
        const senderName = chatElement.querySelector('p').textContent; // Grab the sender's name
    
        // Update the header with the selected sender_id
        const chatHeader = document.getElementById('chatHeader');
        if (chatHeader) {
            chatHeader.textContent = senderName || senderId;
        }
    
        // Update the hidden input field with the selected sender_id
        document.getElementById('selectedSenderId').value = senderId;
    
        // Dynamically update the ig_id field
        const igIdField = document.querySelector('[name="ig_id"]');
        if (igIdField) {
            igIdField.value = igId;
        }
    
        // Add a loading indicator to the messages container
        chatBody.innerHTML = '<p class="text-gray-500 text-center">Loading messages...</p>';
    
        // Show chat footer and header when a chat is selected
        if (footer) {
            footer.classList.remove('hidden');
        }
    
        const chatNav = document.getElementById('chatNav');
        if (chatNav) {
            chatNav.classList.remove('hidden');
        }
    
        // Fetch messages for the selected chat
        fetch(`fetch_messages.php?sender_id=${senderId}`)
            .then(response => response.text())
            .then(messagesHtml => {
                // Replace the messages container with fetched messages
                chatBody.innerHTML = messagesHtml;
                
                 // Scroll to the bottom after rendering messages
                setTimeout(() => {
                    chatArea.scrollTop = chatArea.scrollHeight;
                    }, 100);
                    
                // Highlight the selected chat in the chat list
                document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active-chat'));
                chatElement.classList.add('active-chat');
            })
            .catch(error => {
                chatBody.innerHTML = '<p class="text-red-500 text-center">Failed to load messages. Please try again.</p>';
                console.error('Error fetching messages:', error);
            });
    
                // Start real-time message fetching
                fetchMessagesRealTime(senderId);
            }

        
        // Helper function to escape HTML (prevents XSS attacks)
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
</body>

</html>
