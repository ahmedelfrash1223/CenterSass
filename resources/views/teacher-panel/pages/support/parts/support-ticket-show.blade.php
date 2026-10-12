<div>
    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-2">{{ $ticket->subject }}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-{{ $ticket->status->color() }} {{ $ticket->status->textColor() }}">
                            <i class="ph-duotone {{ $ticket->status->icon() }} me-1"></i>
                            {{ $ticket->status->label() }}
                        </span>
                        <span class="badge bg-{{ $ticket->priority->color() }} {{ $ticket->priority->textColor() }}">
                            {{ $ticket->priority->label() }}
                        </span>
                        <span class="badge bg-secondary text-white">
                            <i class="ph-duotone {{ $ticket->category->icon() }} me-1"></i>
                            {{ $ticket->category->label() }}
                        </span>
                        <span class="badge bg-primary text-white">
                            {{ $ticket->reference }}
                        </span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    @if($canClose && !$ticket->isClosed())
                        <button type="button" wire:click="closeTicket" wire:loading.attr="disabled"
                            class="btn btn-outline-danger">
                            <span wire:loading.remove wire:target="closeTicket">
                                <i class="ph-duotone ph-lock me-1"></i>إغلاق التذكرة
                            </span>
                            <span wire:loading wire:target="closeTicket">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                            </span>
                        </button>
                    @endif

                    <a href="{{ route('teacher.support.index') }}" wire:navigate class="btn btn-outline-secondary">
                        <i class="ph-duotone ph-arrow-right me-1"></i>رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'support-success',
        'errorKey' => 'support-error',
    ])

    {{-- Chat --}}
    <div class="card border-0 shadow-sm support-chat-card">
        <div class="card-body support-chat-body" id="support-chat-body">

            {{-- رسالة البداية (التذكرة نفسها) --}}
            @foreach($messages as $message)
                @php $isMine = $message->isFromTeacher(); @endphp
                <div class="chat-message {{ $isMine ? 'chat-mine' : 'chat-theirs' }}" wire:key="msg-{{ $message->id }}">
                    <div class="chat-avatar {{ $isMine ? 'chat-avatar-mine' : 'chat-avatar-theirs' }}">
                        @if($isMine)
                            <i class="ph-duotone ph-user"></i>
                        @else
                            <i class="ph-duotone ph-headset"></i>
                        @endif
                    </div>
                    <div class="chat-bubble {{ $isMine ? 'chat-bubble-mine' : 'chat-bubble-theirs' }}">
                        <div class="chat-sender">
                            {{ $isMine ? 'أنت' : 'فريق الدعم' }}
                        </div>
                        <div class="chat-text">
                            {!! nl2br(e($message->body)) !!}
                        </div>
                        <div class="chat-time">
                            <i class="ph-duotone ph-clock me-1"></i>
                            {{ $message->created_at?->format('Y-m-d h:i A') }}
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Loading عند الإرسال --}}
            <div wire:loading wire:target="sendMessage" class="chat-message chat-mine">
                <div class="chat-avatar chat-avatar-mine">
                    <i class="ph-duotone ph-user"></i>
                </div>
                <div class="chat-bubble chat-bubble-mine chat-bubble-typing">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
            </div>
        </div>

        {{-- Input --}}
        @if(!$ticket->isClosed())
            <div class="card-footer bg-transparent border-top p-3">
                @error('newMessage')
                    <div class="alert alert-danger small mb-2 py-2">{{ $message }}</div>
                @enderror

                <form wire:submit.prevent="sendMessage" class="d-flex align-items-end gap-2">
                    <textarea wire:model.defer="newMessage" rows="1" class="form-control support-chat-input"
                        placeholder="اكتب رسالتك هنا..." wire:keydown.enter.prevent="sendMessage"></textarea>

                    <button type="submit" class="btn btn-primary support-chat-send" wire:loading.attr="disabled"
                        wire:target="sendMessage">
                        <span wire:loading.remove wire:target="sendMessage">
                            <i class="ph-duotone ph-paper-plane-tilt"></i>
                        </span>
                        <span wire:loading wire:target="sendMessage">
                            <span class="spinner-border spinner-border-sm"></span>
                        </span>
                    </button>
                </form>
                <small class="text-body-secondary d-block mt-2">
                    <i class="ph-duotone ph-info me-1"></i>
                    اضغط Enter للإرسال
                </small>
            </div>
        @else
            <div class="card-footer bg-transparent border-top p-3 text-center">
                <div class="alert alert-secondary d-flex align-items-center justify-content-center gap-2 mb-0">
                    <i class="ph-duotone ph-lock fs-5"></i>
                    <span>هذه التذكرة مغلقة ولا يمكن الرد عليها.</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Auto scroll --}}
    @script
    <script>
        function scrollChatToBottom() {
            const el = document.getElementById('support-chat-body');
            if (el) el.scrollTop = el.scrollHeight;
        }
        scrollChatToBottom();
        Livewire.hook('morph.updated', () => setTimeout(scrollChatToBottom, 100));
    </script>
    @endscript
</div>
