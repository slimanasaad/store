<form method="POST" action="{{ route('add_employee') }}">
    @csrf
    <input type="text" name="name">
    <input type="email" name="email">
    <input type="password" name="password">
    <input type="text" name="role_name">
    <button>submit</button>
</form>
