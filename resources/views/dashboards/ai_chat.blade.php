<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('مساعد سندك الذكي') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col h-[600px]">
                <!-- Chat Messages -->
                <div id="chat-box" class="flex-1 p-6 overflow-y-auto space-y-4 bg-gray-50">
                    <div class="flex justify-start">
                        <div class="bg-blue-100 text-blue-800 p-3 rounded-lg max-w-xs">
                            مرحباً بك! أنا مساعدك الذكي في سندك. كيف يمكنني مساعدتك اليوم؟
                        </div>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="p-4 border-t">
                    <form id="chat-form" class="flex gap-2">
                        <input type="text" id="user-input" class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="اسأل عن حالتك الصحية...">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">إرسال</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('user-input');
            const message = input.value.trim();
            if (!message) return;

            // Add user message
            const chatBox = document.getElementById('chat-box');
            chatBox.innerHTML += `<div class="flex justify-end"><div class="bg-blue-600 text-white p-3 rounded-lg max-w-xs">${message}</div></div>`;
            input.value = '';

            // Simulate AI response
            setTimeout(() => {
                chatBox.innerHTML += `<div class="flex justify-start"><div class="bg-blue-100 text-blue-800 p-3 rounded-lg max-w-xs">بناءً على بياناتك الأخيرة، نبضات قلبك مستقرة ولا توجد مؤشرات لنوبة قريبة. استمر في اتباع تعليمات السلامة.</div></div>`;
                chatBox.scrollTop = chatBox.scrollHeight;
            }, 1000);
        });
    </script>
</x-app-layout>
