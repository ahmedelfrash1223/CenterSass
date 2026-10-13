<div>
    <div class="card">
        <div class="card-body">
            <div class="chat-wrapper">
                <div class="chat-content">

                    {{-- ============================
                    Chat Header
                    ============================ --}}
                    <div class="d-flex align-items-center mb-3">
                        <ul class="list-inline me-auto mb-0">
                            <li class="list-inline-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="chat-avtar">
                                            <img class="rounded-circle img-fluid wid-40"
                                                src="{{ asset('assets/teacherPanel/images/favicon.png') }}"
                                                alt="Classio" />
                                            <i class="chat-badge bg-success"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 mx-3 d-none d-sm-inline-block">
                                        <h6 class="mb-0">فريق دعم Classio</h6>
                                        <span class="text-sm text-muted">{{ $ticket->reference }}</span>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <ul class="list-inline ms-auto mb-0">
                            @if($canClose && !$ticket->isClosed())
                                <li class="list-inline-item">
                                    <button type="button" wire:click="closeTicket" wire:loading.attr="disabled"
                                        class="avtar avtar-s btn-link-secondary border-0 bg-transparent"
                                        title="إغلاق التذكرة">
                                        <span wire:loading.remove wire:target="closeTicket">
                                            <i class="ti ti-lock f-18"></i>
                                        </span>
                                        <span wire:loading wire:target="closeTicket">
                                            <span class="spinner-border spinner-border-sm"></span>
                                        </span>
                                    </button>
                                </li>
                            @endif

                            <li class="list-inline-item">
                                <a href="{{ route('teacher.support.index') }}" wire:navigate
                                    class="avtar avtar-s btn-link-secondary" title="رجوع">
                                    <i class="ti ti-arrow-right f-18"></i>
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- ============================
                    Chat Message Board
                    ============================ --}}
                    <div class="card bg-body shadow-none mb-0">
                        <div class="chat-message" id="cl-chat-scroll" wire:poll.5s>
                            <div class="card-body">

                                {{-- Date Separator --}}
                                <div class="text-center mb-3">
                                    <span class="text-muted small">
                                        {{ $ticket->created_at?->translatedFormat('l، d F Y') }}
                                    </span>
                                </div>

                                @foreach($messages as $message)
                                    @php $isMine = $message->isFromTeacher(); @endphp

                                    @if($isMine)
                                        {{-- ============================
                                        Message OUT (المدرس)
                                        ============================ --}}
                                        <div class="message-out" wire:key="msg-{{ $message->id }}">
                                            <div class="d-flex align-items-end flex-column">
                                                <p class="mb-1 text-muted">
                                                    <small>{{ $message->created_at?->format('h:i A') }}</small>
                                                </p>
                                                <div class="message d-flex align-items-end flex-column">
                                                    <div class="d-flex align-items-center mb-1 chat-msg">
                                                        <div class="flex-grow-1 ms-3">
                                                            <div class="msg-content bg-primary">
                                                                <p class="mb-0">{!! nl2br(e($message->body)) !!}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- ============================
                                        Message IN (فريق الدعم)
                                        ============================ --}}
                                        <div class="message-in" wire:key="msg-{{ $message->id }}">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="chat-avtar">
                                                        <img class="rounded-circle img-fluid wid-40"
                                                            src="{{ asset('assets/teacherPanel/images/favicon.png') }}"
                                                            alt="Classio" />
                                                        <i class="chat-badge bg-success"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 mx-3">
                                                    <div class="d-flex align-items-start flex-column">
                                                        <p class="mb-1 text-muted">
                                                            Classio
                                                            <small>{{ $message->created_at?->format('h:i A') }}</small>
                                                        </p>
                                                        <div class="message d-flex align-items-start flex-column">
                                                            <div class="d-flex align-items-center mb-1 chat-msg">
                                                                <div class="flex-grow-1 me-3">
                                                                    <div class="msg-content card mb-0">
                                                                        <p class="mb-0">{!! nl2br(e($message->body)) !!}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                {{-- Typing --}}
                                <div wire:loading.delay.shortest wire:target="sendMessage" class="message-out"
                                    style="display: none;">
                                    <div class="d-flex align-items-end flex-column">
                                        <div class="message d-flex align-items-end flex-column">
                                            <div class="d-flex align-items-center mb-1 chat-msg">
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="msg-content bg-primary">
                                                        <p class="mb-0">
                                                            <span class="typing-dot"></span>
                                                            <span class="typing-dot"></span>
                                                            <span class="typing-dot"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ============================
                    Chat Footer
                    ============================ --}}
                    @if(!$ticket->isClosed())
                        <div class="card-footer border-top pt-2 px-3 pb-0">
                            <div class="input-group align-items-center">
                                <input type="text" wire:model.defer="newMessage" wire:keydown.enter.prevent="sendMessage"
                                    class="form-control shadow-none border-0 bg-transparent"
                                    placeholder="اكتب رسالتك....." />

                                <ul class="list-inline ms-auto mb-0">
                                    <li class="list-inline-item">
                                        <button type="button" wire:click="sendMessage" wire:loading.attr="disabled"
                                            wire:target="sendMessage"
                                            class="avtar avtar-s rounded-circlen btn btn-primary border-0">
                                            <span wire:loading.remove wire:target="sendMessage">
                                                <i class="ti ti-send f-18"></i>
                                            </span>
                                            <span wire:loading wire:target="sendMessage">
                                                <span class="spinner-border spinner-border-sm"></span>
                                            </span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @else
                        <div class="card-footer border-top text-center py-3">
                            <span class="text-muted small">
                                <i class="ti ti-lock me-1"></i>
                                هذه التذكرة مغلقة ولا يمكن الرد عليها
                            </span>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>


    @script
    <script>
        function scrollChatBottom() {
            const el = document.getElementById('cl-chat-scroll');
            if (el) el.scrollTop = el.scrollHeight;
        }

        // scroll بعد أول mount
        setTimeout(scrollChatBottom, 150);

        // scroll بعد أي تحديث Livewire
        Livewire.hook('morph.updated', () => {
            setTimeout(scrollChatBottom, 100);
        });

        // scroll بعد إرسال رسالة
        $wire.on('message-sent', () => {
            setTimeout(scrollChatBottom, 150);
        });
    </script>
    @endscript
</div>
