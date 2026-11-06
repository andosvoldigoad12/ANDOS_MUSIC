/**
 * ANDOSMUSIC - Advanced HTML5 Audio Player Engine
 * Full-featured streaming player with queue, shuffle, repeat, seek, volume & history tracking.
 */

class AndosPlayer {
    constructor() {
        this.audio = new Audio();
        this.queue = [];
        this.currentIndex = -1;
        this.isPlaying = false;
        this.isShuffle = false;
        this.isRepeat = false; // 'none', 'one', 'all'
        this.loggedPlayIds = new Set();
        this.hasLoggedCurrent = false;

        // UI Elements
        this.dom = {
            bar: document.getElementById('musicPlayerBar'),
            cover: document.getElementById('playerCover'),
            title: document.getElementById('playerTitle'),
            artist: document.getElementById('playerArtist'),
            playBtn: document.getElementById('playerPlayBtn'),
            playIcon: document.getElementById('playerPlayIcon'),
            prevBtn: document.getElementById('playerPrevBtn'),
            nextBtn: document.getElementById('playerNextBtn'),
            shuffleBtn: document.getElementById('playerShuffleBtn'),
            repeatBtn: document.getElementById('playerRepeatBtn'),
            progressWrap: document.getElementById('playerProgressWrap'),
            progressFill: document.getElementById('playerProgressFill'),
            currentTime: document.getElementById('playerCurrentTime'),
            totalTime: document.getElementById('playerTotalTime'),
            volumeSlider: document.getElementById('playerVolumeSlider'),
            volumeIcon: document.getElementById('playerVolumeIcon'),
            likeBtn: document.getElementById('playerLikeBtn'),
        };

        this.init();
    }

    init() {
        if (!this.dom.bar) return;

        // Restore Volume
        const savedVolume = localStorage.getItem('andos_volume') ?? 0.8;
        this.audio.volume = parseFloat(savedVolume);
        if (this.dom.volumeSlider) {
            this.dom.volumeSlider.value = this.audio.volume;
        }

        // Event Listeners for Audio
        this.audio.addEventListener('timeupdate', () => this.onTimeUpdate());
        this.audio.addEventListener('loadedmetadata', () => this.onMetadataLoaded());
        this.audio.addEventListener('ended', () => this.onTrackEnded());
        this.audio.addEventListener('error', (e) => this.onAudioError(e));

        // Event Listeners for DOM Controls
        if (this.dom.playBtn) {
            this.dom.playBtn.addEventListener('click', () => this.togglePlay());
        }

        if (this.dom.prevBtn) {
            this.dom.prevBtn.addEventListener('click', () => this.prevTrack());
        }

        if (this.dom.nextBtn) {
            this.dom.nextBtn.addEventListener('click', () => this.nextTrack());
        }

        if (this.dom.shuffleBtn) {
            this.dom.shuffleBtn.addEventListener('click', () => this.toggleShuffle());
        }

        if (this.dom.repeatBtn) {
            this.dom.repeatBtn.addEventListener('click', () => this.toggleRepeat());
        }

        if (this.dom.progressWrap) {
            this.dom.progressWrap.addEventListener('click', (e) => this.seek(e));
        }

        if (this.dom.volumeSlider) {
            this.dom.volumeSlider.addEventListener('input', (e) => this.setVolume(e.target.value));
        }

        if (this.dom.volumeIcon) {
            this.dom.volumeIcon.addEventListener('click', () => this.toggleMute());
        }

        // Restore state if available
        this.restoreState();

        // Listen for global play events
        window.addEventListener('andos:play', (e) => {
            const { track, queue, index } = e.detail;
            if (queue && queue.length > 0) {
                this.setQueue(queue, index !== undefined ? index : 0);
            } else if (track) {
                this.playSingleTrack(track);
            }
        });
    }

    playSingleTrack(track) {
        this.queue = [track];
        this.currentIndex = 0;
        this.loadAndPlayCurrent();
    }

    setQueue(tracks, startIndex = 0) {
        this.queue = [...tracks];
        this.currentIndex = Math.max(0, Math.min(startIndex, this.queue.length - 1));
        this.loadAndPlayCurrent();
    }

    loadAndPlayCurrent() {
        if (this.currentIndex < 0 || this.currentIndex >= this.queue.length) return;
        const track = this.queue[this.currentIndex];

        if (!track || !track.audio_url) {
            this.showToast('Audio file tidak valid atau belum disetujui.', 'error');
            return;
        }

        this.hasLoggedCurrent = false;
        this.audio.src = track.audio_url;

        // Update UI
        if (this.dom.cover) this.dom.cover.src = track.cover_url || '/images/default-album.svg';
        if (this.dom.title) this.dom.title.textContent = track.title || 'Untitled Track';
        if (this.dom.artist) this.dom.artist.textContent = track.artist || 'Unknown Artist';
        if (this.dom.totalTime) this.dom.totalTime.textContent = this.formatTime(track.duration || 0);
        if (this.dom.progressFill) this.dom.progressFill.style.width = '0%';
        if (this.dom.currentTime) this.dom.currentTime.textContent = '00:00';

        // Check favorite status if provided
        if (this.dom.likeBtn) {
            this.dom.likeBtn.dataset.musicId = track.id;
            const heartIcon = this.dom.likeBtn.querySelector('i');
            if (heartIcon) {
                heartIcon.className = track.is_favorited ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart';
            }
        }

        this.audio.play().then(() => {
            this.isPlaying = true;
            this.updatePlayBtnUI();
            this.saveState();
        }).catch(err => {
            console.warn('Auto-play blocked or audio load error:', err);
            this.isPlaying = false;
            this.updatePlayBtnUI();
        });
    }

    togglePlay() {
        if (!this.audio.src || this.queue.length === 0) return;

        if (this.audio.paused) {
            this.audio.play().then(() => {
                this.isPlaying = true;
                this.updatePlayBtnUI();
            }).catch(err => {
                this.showToast('Gagal memutar audio. Pastikan file tersedia.', 'error');
            });
        } else {
            this.audio.pause();
            this.isPlaying = false;
            this.updatePlayBtnUI();
        }
        this.saveState();
    }

    prevTrack() {
        if (this.queue.length === 0) return;
        // If current playback > 3 seconds, restart current track
        if (this.audio.currentTime > 3) {
            this.audio.currentTime = 0;
            return;
        }

        if (this.currentIndex > 0) {
            this.currentIndex--;
        } else {
            this.currentIndex = this.queue.length - 1;
        }
        this.loadAndPlayCurrent();
    }

    nextTrack() {
        if (this.queue.length === 0) return;

        if (this.isShuffle && this.queue.length > 1) {
            let nextIdx;
            do {
                nextIdx = Math.floor(Math.random() * this.queue.length);
            } while (nextIdx === this.currentIndex);
            this.currentIndex = nextIdx;
        } else if (this.currentIndex < this.queue.length - 1) {
            this.currentIndex++;
        } else {
            this.currentIndex = 0; // Loop back
        }
        this.loadAndPlayCurrent();
    }

    toggleShuffle() {
        this.isShuffle = !this.isShuffle;
        if (this.dom.shuffleBtn) {
            this.dom.shuffleBtn.classList.toggle('active', this.isShuffle);
        }
        this.showToast(this.isShuffle ? 'Shuffle diaktifkan' : 'Shuffle dinonaktifkan', 'info');
    }

    toggleRepeat() {
        this.isRepeat = !this.isRepeat;
        if (this.dom.repeatBtn) {
            this.dom.repeatBtn.classList.toggle('active', this.isRepeat);
        }
        this.showToast(this.isRepeat ? 'Repeat aktif' : 'Repeat nonaktif', 'info');
    }

    seek(e) {
        if (!this.audio.duration || isNaN(this.audio.duration)) return;
        const rect = this.dom.progressWrap.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const width = rect.width;
        const percent = Math.max(0, Math.min(1, clickX / width));
        this.audio.currentTime = percent * this.audio.duration;
    }

    setVolume(value) {
        const vol = parseFloat(value);
        this.audio.volume = vol;
        localStorage.setItem('andos_volume', vol);
        this.updateVolumeIcon(vol);
    }

    toggleMute() {
        if (this.audio.volume > 0) {
            this.previousVolume = this.audio.volume;
            this.setVolume(0);
            if (this.dom.volumeSlider) this.dom.volumeSlider.value = 0;
        } else {
            const restored = this.previousVolume || 0.8;
            this.setVolume(restored);
            if (this.dom.volumeSlider) this.dom.volumeSlider.value = restored;
        }
    }

    updateVolumeIcon(vol) {
        if (!this.dom.volumeIcon) return;
        if (vol === 0) {
            this.dom.volumeIcon.className = 'fa-solid fa-volume-xmark';
        } else if (vol < 0.5) {
            this.dom.volumeIcon.className = 'fa-solid fa-volume-low';
        } else {
            this.dom.volumeIcon.className = 'fa-solid fa-volume-high';
        }
    }

    onTimeUpdate() {
        if (!this.audio.duration || isNaN(this.audio.duration)) return;

        const current = this.audio.currentTime;
        const duration = this.audio.duration;
        const percent = (current / duration) * 100;

        if (this.dom.progressFill) {
            this.dom.progressFill.style.width = `${percent}%`;
        }
        if (this.dom.currentTime) {
            this.dom.currentTime.textContent = this.formatTime(current);
        }

        // Register play count after 4 seconds of playback
        if (current >= 4 && !this.hasLoggedCurrent && this.queue[this.currentIndex]) {
            this.hasLoggedCurrent = true;
            this.logPlayCount(this.queue[this.currentIndex].id);
        }
    }

    onMetadataLoaded() {
        if (this.dom.totalTime && !isNaN(this.audio.duration)) {
            this.dom.totalTime.textContent = this.formatTime(this.audio.duration);
        }
    }

    onTrackEnded() {
        if (this.isRepeat) {
            this.audio.currentTime = 0;
            this.audio.play();
        } else {
            this.nextTrack();
        }
    }

    onAudioError(e) {
        console.error('Audio playback error', e);
        this.isPlaying = false;
        this.updatePlayBtnUI();
        this.showToast('Gagal memuat file audio atau format tidak didukung.', 'error');
    }

    updatePlayBtnUI() {
        if (!this.dom.playIcon) return;
        if (this.isPlaying && !this.audio.paused) {
            this.dom.playIcon.className = 'fa-solid fa-pause';
        } else {
            this.dom.playIcon.className = 'fa-solid fa-play';
        }
    }

    formatTime(seconds) {
        const sec = Math.floor(seconds || 0);
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return `${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
    }

    logPlayCount(musicId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch(`/music/${musicId}/play`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'Accept': 'application/json'
            }
        }).then(res => res.json()).then(data => {
            // Update play count in UI if currently on that track's card
            const countEl = document.querySelector(`.play-count-${musicId}`);
            if (countEl && data.plays_count) {
                countEl.textContent = data.plays_count;
            }
        }).catch(err => console.log('Play logging error', err));
    }

    saveState() {
        if (this.currentIndex >= 0 && this.queue[this.currentIndex]) {
            const state = {
                track: this.queue[this.currentIndex],
                queue: this.queue,
                currentIndex: this.currentIndex,
                isShuffle: this.isShuffle,
                isRepeat: this.isRepeat
            };
            sessionStorage.setItem('andos_player_state', JSON.stringify(state));
        }
    }

    restoreState() {
        try {
            const raw = sessionStorage.getItem('andos_player_state');
            if (raw) {
                const state = JSON.parse(raw);
                if (state && state.track) {
                    this.queue = state.queue || [state.track];
                    this.currentIndex = state.currentIndex || 0;
                    this.isShuffle = !!state.isShuffle;
                    this.isRepeat = !!state.isRepeat;

                    const track = state.track;
                    this.audio.src = track.audio_url;

                    if (this.dom.cover) this.dom.cover.src = track.cover_url || '/images/default-album.svg';
                    if (this.dom.title) this.dom.title.textContent = track.title || 'Untitled Track';
                    if (this.dom.artist) this.dom.artist.textContent = track.artist || 'Unknown Artist';
                    if (this.dom.totalTime) this.dom.totalTime.textContent = this.formatTime(track.duration || 0);

                    if (this.dom.shuffleBtn && this.isShuffle) this.dom.shuffleBtn.classList.add('active');
                    if (this.dom.repeatBtn && this.isRepeat) this.dom.repeatBtn.classList.add('active');
                }
            }
        } catch (e) {
            console.warn('Could not restore player state', e);
        }
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `andos-toast andos-toast-${type}`;
        toast.innerHTML = `<i class="fa-solid ${type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-info'}"></i> <span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }
}

// Global Theme Management
window.toggleAndosTheme = function () {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('andos_theme', newTheme);

    const themeIcon = document.getElementById('themeToggleIcon');
    if (themeIcon) {
        themeIcon.className = newTheme === 'light' ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
    }
};

// Initialize on DOM Ready
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Theme from localStorage
    const savedTheme = localStorage.getItem('andos_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
    const themeIcon = document.getElementById('themeToggleIcon');
    if (themeIcon) {
        themeIcon.className = savedTheme === 'light' ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
    }

    // 2. Initialize Player
    window.player = new AndosPlayer();

    // 3. Global Play Button Click Handler
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="play-track"]');
        if (btn) {
            e.preventDefault();
            const trackData = {
                id: btn.dataset.id,
                title: btn.dataset.title,
                artist: btn.dataset.artist,
                cover_url: btn.dataset.cover,
                audio_url: btn.dataset.audio,
                duration: parseInt(btn.dataset.duration || 0),
                is_favorited: btn.dataset.favorited === 'true'
            };
            window.dispatchEvent(new CustomEvent('andos:play', {
                detail: { track: trackData }
            }));
        }
    });

    // 4. Global Like Button Handler
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-action="toggle-like"]');
        if (btn) {
            e.preventDefault();
            const musicId = btn.dataset.musicId;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch(`/music/${musicId}/favorite`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.status === 'success') {
                    // Update all heart icons for this song
                    document.querySelectorAll(`[data-action="toggle-like"][data-music-id="${musicId}"]`).forEach(el => {
                        const icon = el.querySelector('i');
                        if (icon) {
                            if (data.is_favorited) {
                                icon.className = 'fa-solid fa-heart text-danger';
                                icon.style.color = '#EF4444';
                            } else {
                                icon.className = 'fa-regular fa-heart';
                                icon.style.color = '';
                            }
                        }
                    });

                    // Update count if exists
                    const countEl = document.querySelector(`.like-count-${musicId}`);
                    if (countEl && data.total_favorites !== undefined) {
                        countEl.textContent = data.total_favorites;
                    }

                    if (window.player) {
                        window.player.showToast(data.message, 'info');
                    }
                }
            } catch (err) {
                console.error('Like toggle error', err);
            }
        }
    });
});

