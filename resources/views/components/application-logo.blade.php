<div class="flex items-center space-x-2">
   <svg {{ $attributes->merge(['class' => 'flex-shrink-0']) }} viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
       <!-- Optional: Circle Background -->
       <circle cx="50" cy="50" r="45" fill="currentColor" opacity="0.1"/>
       
       <path d="M20 35 
                v20 
                h15
                v-20
                l15 25 
                l15 -25
                v20
                h15" 
             stroke="currentColor" 
             stroke-width="4" 
             fill="none"
             stroke-linecap="round"
             stroke-linejoin="round"/>
   </svg>
</div>