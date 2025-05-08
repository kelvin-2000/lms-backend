describe('Auth API', () => {
  describe('POST /api/register', () => {
    it('should register a new user', async () => {
      const userData = {
        name: 'New User',
        email: 'newuser@example.com',
        password: 'password123',
        password_confirmation: 'password123'
      };

      const response = await request()
        .post('/api/register')
        .send(userData)
        .expect(201);
      
      // Log the response for debugging
      console.log('Register response:', {
        status: response.status,
        contentType: 'application/json',
        body: response.json()
      });

      // Verify that the response contains the expected data
      const responseBody = response.json();
      expect(responseBody.success).toBe(true);
      expect(responseBody.data).toHaveProperty('user');
      expect(responseBody.data).toHaveProperty('access_token');
      expect(responseBody.data.user.email).toBe(userData.email);
    });

    it('should return validation errors for invalid data', async () => {
      const userData = {
        name: 'Test User',
        // Missing email
        password: 'password123'
      };

      const response = await request()
        .post('/api/register')
        .send(userData)
        .expect(422);

      // Verify validation errors
      const responseBody = response.json();
      expect(responseBody).toHaveProperty('message');
    });
  });

  describe('POST /api/login', () => {
    it('should login a user with valid credentials', async () => {
      const credentials = {
        email: 'test@example.com',
        password: 'password123'
      };

      const response = await request()
        .post('/api/login')
        .send(credentials)
        .expect(200);

      // Verify that the request was successful
      const responseBody = response.json();
      expect(responseBody.success).toBe(true);
      expect(responseBody.data).toHaveProperty('user');
      expect(responseBody.data).toHaveProperty('access_token');
    });

    it('should fail with invalid credentials', async () => {
      const credentials = {
        email: 'test@example.com',
        password: 'wrongpassword'
      };

      const response = await request()
        .post('/api/login')
        .send(credentials)
        .expect(401);

      // Verify error response
      const responseBody = response.json();
      expect(responseBody).toHaveProperty('message');
      expect(responseBody).toHaveProperty('errors');
    });
  });

  describe('POST /api/logout', () => {
    it('should logout an authenticated user', async () => {
      // First login to get token
      const token = await loginAsUser({
        email: 'test@example.com',
        password: 'password123'
      });
      
      // Then attempt to logout
      const response = await request()
        .post('/api/logout')
        .set('Authorization', `Bearer ${token}`)
        .expect(200);
      
      // Verify that the logout was successful
      const responseBody = response.json();
      expect(responseBody).toHaveProperty('message');
      expect(responseBody.message).toBe('Logged out successfully');
    });
    
    it('should fail for unauthenticated users', async () => {
      const response = await request()
        .post('/api/logout')
        .expect(401);
      
      // Verify that the request failed due to missing authentication
      const responseBody = response.json();
      expect(responseBody).toHaveProperty('message');
    });
  });
}); 