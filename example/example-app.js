import express from 'express';

const app = express();

const basePasswordHeader = function (req, res, next) {
    console.log('Checking for base header password...');

    // Check if 'x-context-password' is set
    const passwordHeader = req.header('x-context-password');

    if (passwordHeader !== 'password') {
        console.log('header does not match password: ', passwordHeader);

        res.status(401).json({ error: 'Not authenticated' });
        return next('route');
    }

    console.log('header is matching');

    next();
};

app.get('/api/authorize', basePasswordHeader, (req, res) => {
    res.json({
        data: {
            token: 'token123',
        },
    });
});

app.get('/api/me', (req, res, next) => {
    // Check token
    if (req.header('Authorization') !== 'token123') {
        res.status(401).json({ error: 'Not authenticated' });
        return next('route');
    }

    res.json({
        name: 'John Doe',
        email: 'test@example.com',
    });
});

app.listen(3000);
