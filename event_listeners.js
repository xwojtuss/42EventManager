document.addEventListener("DOMContentLoaded", function () {
    document.body.addEventListener("click", function (event) {
        if (event.target.classList.contains("toggle-desc")) {
            let descId = "desc-" + event.target.dataset.id;
            let descDiv = document.getElementById(descId);
            
            event.target.classList.toggle("button-on");
            descDiv.classList.toggle("show");
        }

        if (event.target.classList.contains("show-users-btn")) {
            let eventId = event.target.dataset.id;
            let modalId = "userModal-" + eventId;
            let modal = document.getElementById(modalId);
            let userList = modal.querySelector(".user-list");
            let loadingIndicator = modal.querySelector(".loading-indicator");

            modal.style.display = "flex";
            userList.innerHTML = ""; 
            loadingIndicator.style.display = "block";

            fetchUserData(eventId, modal, userList, loadingIndicator);
        }

        if (event.target.classList.contains("show-feedback-btn")) {
            let eventId = event.target.dataset.id;
            let modalId = "feedbackModal-" + eventId;
            let modal = document.getElementById(modalId);
            let feedbackList = modal.querySelector(".feedback-list");
            let loadingIndicator = modal.querySelector(".loading-indicator");

            modal.style.display = "flex";
            feedbackList.innerHTML = ""; 
            loadingIndicator.style.display = "block";

            fetchFeedbackData(eventId, modal, feedbackList, loadingIndicator);
        }

        if (event.target.classList.contains("close-modal")) {
            event.target.closest(".modal").style.display = "none";
        }

        if (event.target.classList.contains("modal")) {
            event.target.style.display = "none";
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            let openModal = document.querySelector(".modal[style='display: flex;']");
            if (openModal) {
                openModal.style.display = "none";
            }
        }
    });

    function fetchUserData(eventId, modal, userList, loadingIndicator) {
        fetch("fetch_users.php?event_id=" + eventId)
            .then(response => {
                return response.text();
            }) 
            .then(data => {
                if (data.trim() === "") {
                    userList.innerHTML = "<li>No users found.</li>";
                } else {
                    userList.innerHTML = data;
                }
                modal.classList.add("data-loaded");
            })
            .catch(error => {
                console.error("Error fetching users:", error);
                userList.innerHTML = "<li>Error loading users.</li>";
            })
            .finally(() => {
                loadingIndicator.style.display = "none";
            });
    }

    function fetchFeedbackData(eventId, modal, feedbackList, loadingIndicator) {
        fetch("fetch_feedbacks.php?event_id=" + eventId)
            .then(response => {
                return response.text();
            }) 
            .then(data => {
                if (data.trim() === "") {
                    feedbackList.innerHTML = "<li>No feedbacks found.</li>";
                } else {
                    feedbackList.innerHTML = data;
                }
                modal.classList.add("data-loaded");
            })
            .catch(error => {
                console.error("Error fetching feedbacks:", error);
                feedbackList.innerHTML = "<li>Error loading feedbacks.</li>";
            })
            .finally(() => {
                loadingIndicator.style.display = "none";
            });
    }
});
