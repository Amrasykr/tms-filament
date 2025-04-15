<x-blog-layout>

    <section class="container mx-auto pt-4 pb-8">
    <h2>Login</h2>

    @if ($errors->any())
        <p style="color: red">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>    
</section>


</x-blog-layout>
