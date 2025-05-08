import { mockRequest, resetMockDb } from './mocks/api.js';

// Global setup hooks
beforeAll(() => {
  // Setup code before all tests
  console.log('Starting API tests');
});

afterAll(() => {
  // Cleanup after all tests are finished
  console.log('Completed API tests');
});

beforeEach(() => {
  // Reset mock database before each test
  resetMockDb();
});

// Make the mock request function available globally
global.request = mockRequest;

// Setup function for auth testing
global.loginAsUser = async (credentials = { email: 'test@example.com', password: 'password123' }) => {
  const response = await request()
    .post('/api/login')
    .send(credentials);
  
  // Return the authentication token from the response
  return response.json().data.access_token;
}; 