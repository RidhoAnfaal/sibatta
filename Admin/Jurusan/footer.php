<style>
html, body {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

.container {
    flex: 1; /* This allows the container to grow and take up available space */
}

.footer {
    background-color: #000;
    color: #ffffff;
    padding: 10px 15px;
    text-align: center;
    font-size: 0.8rem;
    width: 100%;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
}

.footer p {
    margin: 5px 0;
    line-height: 1.4;
}

.footer a {
    color: #ffc107;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}

.footer a:hover {
    text-decoration: underline;
}

.social-icons a {
    font-size: 1.2rem;
    color: #ffc107;
    margin: 0 5px;
    transition: color 0.3s ease;
}

.social-icons a:hover {
    color: #ffffff;
}
</style>

<footer class="footer mt-auto py-4">
    <div class="container text-center">
        <p>&copy; 2024 <strong>SIBATTA</strong>. All rights reserved.</p>
        <p>Contact us: <a href="mailto:support@sibatta.com">support@sibatta.com</a></p>
        <div class="social-icons">
            <a href="https://facebook.com" target="_blank" class="me-3">
                <i class="bi bi-facebook"></i>
            </a>
            <a href="https://twitter.com" target="_blank" class="me-3">
                <i class="bi bi-twitter"></i>
            </a>
            <a href="https://instagram.com" target="_blank">
                <i class="bi bi-instagram"></i>
            </a>
        </div>
    </div>
</footer>