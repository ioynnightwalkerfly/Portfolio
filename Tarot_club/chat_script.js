document.addEventListener("DOMContentLoaded", function() {
    const chatBox = document.getElementById("chat-box");
    const chatInput = document.getElementById("chat-input");
    const sendBtn = document.getElementById("send-btn");

    // ฟังก์ชันสำหรับเพิ่มข้อความลงในกล่องแชท
    function addMessage(text, className) {
        const messageDiv = document.createElement("div");
        messageDiv.classList.add("chat-message", className);
        messageDiv.textContent = text;
        chatBox.appendChild(messageDiv);
        
        // เลื่อนลงไปที่ข้อความล่าสุด
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // ฟังก์ชันส่งข้อความ
    function sendMessage() {
        const message = chatInput.value.trim();
        if (message === "") return;

        // เพิ่มข้อความของผู้ใช้
        addMessage(message, "user-message");

        // ตอบกลับอัตโนมัติ
        setTimeout(() => {
            addMessage("บอท: ได้รับข้อความแล้ว!", "bot-message");
        }, 1000);

        // ล้างช่องพิมพ์
        chatInput.value = "";
    }

    // กดปุ่มส่ง
    sendBtn.addEventListener("click", sendMessage);

    // กด Enter เพื่อส่งข้อความ
    chatInput.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            sendMessage();
        }
    });
});
