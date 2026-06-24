<div
    id="cabina-sound-alert"
    style="
        position: fixed;
        right: 18px;
        bottom: 18px;
        z-index: 99999;
        display: flex;
        gap: 8px;
        align-items: center;
    "
>
    <button
        id="cabina-sound-button"
        type="button"
        style="
            padding: 10px 14px;
            border-radius: 10px;
            background: #f59e0b;
            color: #111827;
            font-weight: 700;
            font-size: 13px;
            box-shadow: 0 10px 25px rgba(0,0,0,.25);
            border: none;
            cursor: pointer;
        "
    >
        Activar sonido de cabina
    </button>

    <button
        id="cabina-sound-disable-button"
        type="button"
        style="
            display: none;
            padding: 10px 14px;
            border-radius: 10px;
            background: #374151;
            color: white;
            font-weight: 700;
            font-size: 13px;
            box-shadow: 0 10px 25px rgba(0,0,0,.25);
            border: none;
            cursor: pointer;
        "
    >
        Desactivar
    </button>

    <div
        id="cabina-sound-status"
        style="
            display: none;
            padding: 10px 14px;
            border-radius: 10px;
            background: #16a34a;
            color: white;
            font-weight: 700;
            font-size: 13px;
            box-shadow: 0 10px 25px rgba(0,0,0,.25);
        "
    >
        Sonido activo
    </div>
</div>

<script>
    (() => {
        const countUrl = '{{ route('cabina.notifications.count') }}'
        const storageKey = 'cabina_sound_enabled'

        if (! window.cabinaSound) {
            window.cabinaSound = {
                enabled: localStorage.getItem(storageKey) === '1',
                pendingCount: 0,
                alarmIsPlaying: false,
                intervalId: null,
                audioContext: null,
                documentClickRegistered: false,

                button: null,
                disableButton: null,
                status: null,

                attach() {
                    this.button = document.getElementById('cabina-sound-button')
                    this.disableButton = document.getElementById('cabina-sound-disable-button')
                    this.status = document.getElementById('cabina-sound-status')

                    if (! this.button || ! this.disableButton || ! this.status) {
                        return
                    }

                    this.button.onclick = () => this.enable()
                    this.disableButton.onclick = () => this.disable()

                    if (! this.documentClickRegistered) {
                        document.addEventListener('click', () => {
                            if (this.enabled) {
                                this.initAudio()
                            }
                        })

                        this.documentClickRegistered = true
                    }

                    this.refreshUi()

                    if (this.enabled) {
                        this.startChecking()
                    }
                },

                enable() {
                    this.enabled = true
                    localStorage.setItem(storageKey, '1')

                    this.initAudio()
                    this.beepOnce(1040, 0.25)
                    this.startChecking()
                    this.checkNotifications()
                    this.refreshUi()
                },

                disable() {
                    this.enabled = false
                    this.pendingCount = 0
                    localStorage.removeItem(storageKey)

                    if (this.intervalId) {
                        clearInterval(this.intervalId)
                        this.intervalId = null
                    }

                    this.refreshUi()
                },

                refreshUi() {
                    if (! this.button || ! this.disableButton || ! this.status) {
                        return
                    }

                    if (this.enabled) {
                        this.button.style.display = 'none'
                        this.disableButton.style.display = 'inline-block'
                        this.status.style.display = 'block'

                        if (this.pendingCount > 0) {
                            this.status.innerText = 'Sonido activo · Pendientes: ' + this.pendingCount
                            this.status.style.background = '#dc2626'
                        } else {
                            this.status.innerText = 'Sonido activo'
                            this.status.style.background = '#16a34a'
                        }
                    } else {
                        this.button.style.display = 'inline-block'
                        this.disableButton.style.display = 'none'
                        this.status.style.display = 'none'
                    }
                },

                initAudio() {
                    if (! this.audioContext) {
                        this.audioContext = new (window.AudioContext || window.webkitAudioContext)()
                    }

                    if (this.audioContext.state === 'suspended') {
                        this.audioContext.resume()
                    }
                },

                beepOnce(frequency = 880, duration = 0.35) {
                    this.initAudio()

                    if (! this.audioContext) {
                        return
                    }

                    const oscillator = this.audioContext.createOscillator()
                    const gainNode = this.audioContext.createGain()

                    oscillator.type = 'sine'
                    oscillator.frequency.setValueAtTime(frequency, this.audioContext.currentTime)

                    gainNode.gain.setValueAtTime(0.45, this.audioContext.currentTime)

                    oscillator.connect(gainNode)
                    gainNode.connect(this.audioContext.destination)

                    oscillator.start()
                    oscillator.stop(this.audioContext.currentTime + duration)
                },

                alarmBeep() {
                    if (this.alarmIsPlaying) {
                        return
                    }

                    this.alarmIsPlaying = true

                    const totalBeeps = 12
                    const intervalMs = 550

                    for (let i = 0; i < totalBeeps; i++) {
                        setTimeout(() => {
                            const frequency = i % 2 === 0 ? 880 : 1040
                            this.beepOnce(frequency, 0.35)
                        }, i * intervalMs)
                    }

                    setTimeout(() => {
                        this.alarmIsPlaying = false
                    }, totalBeeps * intervalMs + 500)
                },

                async checkNotifications() {
                    if (! this.enabled) {
                        return
                    }

                    try {
                        const response = await fetch(countUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        })

                        const data = await response.json()

                        this.pendingCount = Number(data.count)
                        this.refreshUi()

                        if (this.pendingCount > 0) {
                            this.alarmBeep()
                        }
                    } catch (error) {
                        console.error('Error revisando notificaciones de cabina:', error)
                    }
                },

                startChecking() {
                    if (this.intervalId) {
                        return
                    }

                    this.checkNotifications()

                    this.intervalId = setInterval(() => {
                        this.checkNotifications()
                    }, 5000)
                },
            }
        }

        window.cabinaSound.attach()
    })()
</script>