<x-filament-widgets::widget>
    <x-filament::section>
        <div
            x-data="{
                enabled: false,
                pendingCount: 0,
                alarmIsPlaying: false,
                intervalId: null,
                audioContext: null,

                init() {
                    this.enabled = localStorage.getItem('cabina_sound_enabled') === '1'

                    if (this.enabled) {
                        this.startChecking()
                    }

                    // Ayuda a desbloquear el audio si el navegador lo suspende después de recargar.
                    document.addEventListener('click', () => {
                        if (this.enabled) {
                            this.initAudio()
                        }
                    })
                },

                destroy() {
                    if (this.intervalId) {
                        clearInterval(this.intervalId)
                    }
                },

                enableSound() {
                    this.enabled = true

                    localStorage.setItem('cabina_sound_enabled', '1')

                    this.initAudio()
                    this.beepOnce(1040, 0.25)
                    this.startChecking()
                },

                disableSound() {
                    this.enabled = false
                    this.pendingCount = 0

                    localStorage.removeItem('cabina_sound_enabled')

                    if (this.intervalId) {
                        clearInterval(this.intervalId)
                        this.intervalId = null
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

                    // Volumen. Puedes subirlo a 0.60 si quieres más fuerte.
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

                    // Duración aproximada: 7 segundos.
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
                        const response = await fetch('{{ route('cabina.notifications.count') }}', {
                            headers: {
                                'Accept': 'application/json',
                            },
                        })

                        const data = await response.json()

                        this.pendingCount = Number(data.count)

                        // Mientras existan notificaciones pendientes, seguirá sonando.
                        if (this.pendingCount > 0) {
                            this.alarmBeep()
                        }

                    } catch (error) {
                        console.error('Error checking notifications', error)
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
            }"
            class="flex items-center justify-between gap-4"
        >
            <div>
                <div class="text-sm font-medium">
                    Sonido de cabina
                </div>

                <div class="text-xs text-gray-500" x-show="! enabled">
                    Activa una alerta sonora para servicios nuevos. Solo se debe activar una vez en este navegador.
                </div>

                <div class="text-xs text-gray-500" x-show="enabled && pendingCount === 0">
                    Sonido activo. Sin servicios pendientes.
                </div>

                <div class="text-xs font-semibold text-danger-600" x-show="enabled && pendingCount > 0">
                    Hay <span x-text="pendingCount"></span> notificación(es) pendiente(s).
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    x-show="! enabled"
                    x-on:click="enableSound"
                    class="fi-btn fi-btn-size-sm fi-color-primary"
                >
                    Activar sonido
                </button>

                <button
                    type="button"
                    x-show="enabled"
                    x-on:click="disableSound"
                    class="fi-btn fi-btn-size-sm fi-color-gray"
                >
                    Desactivar
                </button>

                <div
                    x-show="enabled"
                    class="text-sm text-success-600 font-medium"
                >
                    Sonido activo
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>