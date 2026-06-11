<!DOCTYPE html><html><body>
<form method="POST" action="/login">
@csrf
<input name="email" type="email">
<input name="password" type="password">
<button type="submit">Login</button>
</form>
</body></html>
