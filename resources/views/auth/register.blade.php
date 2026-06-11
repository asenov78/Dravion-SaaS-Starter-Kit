<!DOCTYPE html><html><body>
<form method="POST" action="/register">
@csrf
<input name="name"><input name="email" type="email">
<input name="password" type="password"><input name="password_confirmation" type="password">
<button type="submit">Register</button>
</form>
</body></html>
