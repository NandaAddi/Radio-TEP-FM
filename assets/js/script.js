const songs = [
    { title: "Song 1", src: "music/song1.mp3" },
    { title: "Song 2", src: "music/song2.mp3" },
    { title: "Song 3", src: "music/song3.mp3" }
];

let currentSongIndex = 0;
const audio = document.getElementById("audio");
const playPauseBtn = document.getElementById("play-pause");
const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");
const progress = document.getElementById("progress");
const songTitle = document.getElementById("song-title");

function updateSong() {
    audio.src = songs[currentSongIndex].src;
    songTitle.textContent = songs[currentSongIndex].title;
    audio.play();
    playPauseBtn.innerHTML = '<i data-lucide="pause"></i>';
    lucide.createIcons(); // Refresh icon
}

playPauseBtn.addEventListener("click", () => {
    if (audio.paused) {
        audio.play();
        playPauseBtn.innerHTML = '<i data-lucide="pause"></i>';
    } else {
        audio.pause();
        playPauseBtn.innerHTML = '<i data-lucide="play"></i>';
    }
    lucide.createIcons();
});

prevBtn.addEventListener("click", () => {
    currentSongIndex = (currentSongIndex - 1 + songs.length) % songs.length;
    updateSong();
});

nextBtn.addEventListener("click", () => {
    currentSongIndex = (currentSongIndex + 1) % songs.length;
    updateSong();
});

audio.addEventListener("timeupdate", () => {
    progress.value = (audio.currentTime / audio.duration) * 100;
});

progress.addEventListener("input", () => {
    audio.currentTime = (progress.value / 100) * audio.duration;
});


document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk memuat komentar
    async function loadComments() {
        try {
            const response = await fetch('php/get_comments.php');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Gagal memuat komentar');
            }
            
            const commentsContainer = document.getElementById('comments-list');
            commentsContainer.innerHTML = '';
            
            if (result.data.length === 0) {
                commentsContainer.innerHTML = '<p>Belum ada komentar.</p>';
                return;
            }
            
            result.data.forEach(comment => {
                const commentDate = new Date(comment.created_at);
                const formattedDate = commentDate.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                commentsContainer.innerHTML += `
                    <div class="blog-single-comment d-flex gap-4 pt-4 pb-5">
                        <div class="image">
                            <img src="assets/img/news/comment.jpg" alt="${comment.name}">
                        </div>
                        <div class="content">
                            <div class="head d-flex flex-wrap gap-2 align-items-center justify-content-between">
                                <div class="con">
                                    <h5>${comment.name}</h5>
                                    <span>${formattedDate}</span>
                                </div>
                            </div>
                            <p class="mt-30 mb-4">${comment.message}</p>
                        </div>
                    </div>
                `;
            });
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('comments-list').innerHTML = `<p class="text-danger">${error.message}</p>`;
        }
    }

    // Handle form submission
    document.getElementById('comment-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim...';
        
        try {
            const formData = new FormData(this);
            const response = await fetch('php/submit.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (!response.ok || !result.success) {
                throw new Error(result.error || 'Terjadi kesalahan saat mengirim komentar');
            }
            
            alert('Komentar berhasil dikirim!');
            this.reset();
            await loadComments();
        } catch (error) {
            console.error('Error:', error);
            alert(error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });

    // Muat komentar pertama kali
    loadComments();
});