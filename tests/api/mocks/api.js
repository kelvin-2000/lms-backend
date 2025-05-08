// Mock user data
const users = [
  { 
    id: 1, 
    name: 'Test User', 
    email: 'test@example.com', 
    password: 'password123',
    role: 'student'
  },
  { 
    id: 2, 
    name: 'Admin User', 
    email: 'admin@example.com', 
    password: 'password123',
    role: 'admin'
  },
  { 
    id: 3, 
    name: 'Instructor', 
    email: 'instructor@example.com', 
    password: 'password123',
    role: 'instructor'
  }
];

// Mock courses data
const courses = [
  { 
    id: 1, 
    title: 'Introduction to Laravel', 
    description: 'Learn the basics of Laravel',
    instructor_id: 3,
    price: 199.99,
    created_at: '2023-01-01 00:00:00',
    updated_at: '2023-01-01 00:00:00'
  },
  { 
    id: 2, 
    title: 'Advanced Vue.js', 
    description: 'Master Vue.js development',
    instructor_id: 3,
    price: 299.99,
    created_at: '2023-01-02 00:00:00',
    updated_at: '2023-01-02 00:00:00'
  }
];

// Mock events data
const events = [
  {
    id: 1,
    title: 'Laravel Conference',
    description: 'Annual Laravel conference',
    date: '2023-12-01',
    location: 'New York',
    created_at: '2023-01-01 00:00:00',
    updated_at: '2023-01-01 00:00:00'
  },
  {
    id: 2,
    title: 'Vue.js Workshop',
    description: 'Hands-on Vue.js workshop',
    date: '2023-12-15',
    location: 'San Francisco',
    created_at: '2023-01-02 00:00:00',
    updated_at: '2023-01-02 00:00:00'
  }
];

// Mock data store
const db = {
  users: [...users],
  courses: [...courses],
  events: [...events],
  enrollments: [],
  eventRegistrations: [],
  tokens: {}
};

// Helper functions
const findUser = (email) => db.users.find(user => user.email === email);
const authenticate = (token) => {
  const userId = Object.keys(db.tokens).find(key => db.tokens[key] === token);
  return userId ? db.users.find(user => user.id === parseInt(userId)) : null;
};

// Mock API responses
export const mockApi = {
  // Auth endpoints
  register: (userData) => {
    // Check if user already exists
    if (findUser(userData.email)) {
      return {
        status: 422,
        json: () => ({ 
          message: 'The email has already been taken.',
          errors: { email: ['The email has already been taken.'] }
        })
      };
    }
    
    // Validate required fields
    const requiredFields = ['name', 'email', 'password'];
    const missingFields = requiredFields.filter(field => !userData[field]);
    
    if (missingFields.length > 0) {
      return {
        status: 422,
        json: () => ({ 
          message: 'The given data was invalid.',
          errors: missingFields.reduce((acc, field) => {
            acc[field] = [`The ${field} field is required.`];
            return acc;
          }, {})
        })
      };
    }
    
    // Create new user
    const newUser = {
      id: db.users.length + 1,
      name: userData.name,
      email: userData.email,
      password: userData.password,
      role: 'student'
    };
    
    db.users.push(newUser);
    
    // Generate token
    const token = `mock-token-${newUser.id}-${Date.now()}`;
    db.tokens[newUser.id] = token;
    
    return {
      status: 201,
      json: () => ({
        success: true,
        message: 'User registered successfully',
        data: {
          user: { id: newUser.id, name: newUser.name, email: newUser.email },
          access_token: token,
          token_type: 'Bearer'
        }
      })
    };
  },
  
  login: (credentials) => {
    const user = findUser(credentials.email);
    
    // Check credentials
    if (!user || user.password !== credentials.password) {
      return {
        status: 401,
        json: () => ({ 
          message: 'Invalid credentials',
          errors: { email: ['These credentials do not match our records.'] }
        })
      };
    }
    
    // Generate token
    const token = `mock-token-${user.id}-${Date.now()}`;
    db.tokens[user.id] = token;
    
    return {
      status: 200,
      json: () => ({
        success: true,
        message: 'User logged in successfully',
        data: {
          user: { id: user.id, name: user.name, email: user.email },
          access_token: token,
          token_type: 'Bearer'
        }
      })
    };
  },
  
  logout: (token) => {
    const user = authenticate(token);
    
    if (!user) {
      return {
        status: 401,
        json: () => ({ message: 'Unauthenticated.' })
      };
    }
    
    // Remove token
    delete db.tokens[user.id];
    
    return {
      status: 200,
      json: () => ({ message: 'Logged out successfully' })
    };
  },
  
  // Courses endpoints
  getCourses: (query = {}) => {
    let filteredCourses = [...db.courses];
    
    // Apply filters if provided
    if (query.search) {
      const searchLower = query.search.toLowerCase();
      filteredCourses = filteredCourses.filter(course => 
        course.title.toLowerCase().includes(searchLower) || 
        course.description.toLowerCase().includes(searchLower)
      );
    }
    
    return {
      status: 200,
      json: () => filteredCourses
    };
  },
  
  getCourse: (id) => {
    const course = db.courses.find(course => course.id === parseInt(id));
    
    if (!course) {
      return {
        status: 404,
        json: () => ({ message: 'Course not found' })
      };
    }
    
    return {
      status: 200,
      json: () => course
    };
  },
  
  createCourse: (courseData, token) => {
    const user = authenticate(token);
    
    // Check authentication
    if (!user) {
      return {
        status: 401,
        json: () => ({ message: 'Unauthenticated.' })
      };
    }
    
    // Check authorization
    if (user.role !== 'instructor' && user.role !== 'admin') {
      return {
        status: 403,
        json: () => ({ message: 'Unauthorized action.' })
      };
    }
    
    // Validate data
    const requiredFields = ['title', 'description', 'price'];
    const missingFields = requiredFields.filter(field => !courseData[field]);
    
    if (missingFields.length > 0) {
      return {
        status: 422,
        json: () => ({ 
          message: 'The given data was invalid.',
          errors: missingFields.reduce((acc, field) => {
            acc[field] = [`The ${field} field is required.`];
            return acc;
          }, {})
        })
      };
    }
    
    // Create course
    const newCourse = {
      id: db.courses.length + 1,
      ...courseData,
      instructor_id: user.id,
      created_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
      updated_at: new Date().toISOString().slice(0, 19).replace('T', ' ')
    };
    
    db.courses.push(newCourse);
    
    return {
      status: 201,
      json: () => ({ 
        message: 'Course created successfully',
        data: newCourse
      })
    };
  },
  
  updateCourse: (id, courseData, token) => {
    const user = authenticate(token);
    const courseIndex = db.courses.findIndex(course => course.id === parseInt(id));
    
    // Check if course exists
    if (courseIndex === -1) {
      return {
        status: 404,
        json: () => ({ message: 'Course not found' })
      };
    }
    
    // Check authentication
    if (!user) {
      return {
        status: 401,
        json: () => ({ message: 'Unauthenticated.' })
      };
    }
    
    // Check authorization
    if (user.role !== 'instructor' && user.role !== 'admin' && 
        db.courses[courseIndex].instructor_id !== user.id) {
      return {
        status: 403,
        json: () => ({ message: 'Unauthorized action.' })
      };
    }
    
    // Update course
    const updatedCourse = {
      ...db.courses[courseIndex],
      ...courseData,
      updated_at: new Date().toISOString().slice(0, 19).replace('T', ' ')
    };
    
    db.courses[courseIndex] = updatedCourse;
    
    return {
      status: 200,
      json: () => ({ 
        message: 'Course updated successfully',
        data: updatedCourse
      })
    };
  },
  
  deleteCourse: (id, token) => {
    const user = authenticate(token);
    const courseIndex = db.courses.findIndex(course => course.id === parseInt(id));
    
    // Check if course exists
    if (courseIndex === -1) {
      return {
        status: 404,
        json: () => ({ message: 'Course not found' })
      };
    }
    
    // Check authentication
    if (!user) {
      return {
        status: 401,
        json: () => ({ message: 'Unauthenticated.' })
      };
    }
    
    // Check authorization
    if (user.role !== 'instructor' && user.role !== 'admin' && 
        db.courses[courseIndex].instructor_id !== user.id) {
      return {
        status: 403,
        json: () => ({ message: 'Unauthorized action.' })
      };
    }
    
    // Delete course
    db.courses.splice(courseIndex, 1);
    
    return {
      status: 204,
      json: () => null
    };
  },
  
  // Events endpoints
  getUpcomingEvents: () => {
    // Filter for future events
    const now = new Date();
    const upcomingEvents = db.events.filter(event => new Date(event.date) >= now);
    
    return {
      status: 200,
      json: () => upcomingEvents
    };
  },
  
  getEvent: (id) => {
    const event = db.events.find(event => event.id === parseInt(id));
    
    if (!event) {
      return {
        status: 404,
        json: () => ({ message: 'Event not found' })
      };
    }
    
    return {
      status: 200,
      json: () => event
    };
  },
  
  createEvent: (eventData, token) => {
    const user = authenticate(token);
    
    // Check authentication
    if (!user) {
      return {
        status: 401,
        json: () => ({ message: 'Unauthenticated.' })
      };
    }
    
    // Check authorization
    if (user.role !== 'admin') {
      return {
        status: 403,
        json: () => ({ message: 'Unauthorized action.' })
      };
    }
    
    // Validate data
    const requiredFields = ['title', 'description', 'date', 'location'];
    const missingFields = requiredFields.filter(field => !eventData[field]);
    
    if (missingFields.length > 0) {
      return {
        status: 422,
        json: () => ({ 
          message: 'The given data was invalid.',
          errors: missingFields.reduce((acc, field) => {
            acc[field] = [`The ${field} field is required.`];
            return acc;
          }, {})
        })
      };
    }
    
    // Create event
    const newEvent = {
      id: db.events.length + 1,
      ...eventData,
      created_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
      updated_at: new Date().toISOString().slice(0, 19).replace('T', ' ')
    };
    
    db.events.push(newEvent);
    
    return {
      status: 201,
      json: () => ({ 
        message: 'Event created successfully',
        data: newEvent
      })
    };
  }
};

// Mock supertest
export const mockSupertest = (baseUrl) => {
  return {
    get: (path) => createRequest('GET', path),
    post: (path) => createRequest('POST', path),
    put: (path) => createRequest('PUT', path),
    delete: (path) => createRequest('DELETE', path),
    patch: (path) => createRequest('PATCH', path)
  };
};

// Helper for creating mock requests
function createRequest(method, path) {
  let req = {
    method,
    path,
    _headers: {},
    _body: null,
    _expectedStatus: null,
    _expectedContentType: null,
    
    set: function(header, value) {
      this._headers[header] = value;
      return this;
    },
    
    send: function(data) {
      this._body = data;
      return this;
    },
    
    expect: function(field, matcher) {
      if (typeof field === 'number') {
        this._expectedStatus = field;
      } else if (field === 'Content-Type') {
        this._expectedContentType = matcher;
      }
      return this;
    },
    
    then: function(callback) {
      return Promise.resolve().then(() => {
        const response = this._getResponse();
        
        // Check expected status if set
        if (this._expectedStatus !== null && response.status !== this._expectedStatus) {
          throw new Error(`expected ${this._expectedStatus} but got ${response.status}`);
        }
        
        // Check expected content type if set
        if (this._expectedContentType) {
          const contentType = 'application/json';
          if (!this._expectedContentType.test(contentType)) {
            throw new Error(`expected "Content-Type" matching ${this._expectedContentType}, got "${contentType}"`);
          }
        }
        
        return callback(response);
      });
    },
    
    _getResponse: function() {
      let token = null;
      
      // Extract token from Authorization header if exists
      if (this._headers.Authorization) {
        const match = this._headers.Authorization.match(/Bearer\s+(.+)/);
        if (match) {
          token = match[1];
        }
      }
      
      // Route handling
      if (this.path === '/api/register' && this.method === 'POST') {
        return mockApi.register(this._body);
      }
      
      if (this.path === '/api/login' && this.method === 'POST') {
        return mockApi.login(this._body);
      }
      
      if (this.path === '/api/logout' && this.method === 'POST') {
        return mockApi.logout(token);
      }
      
      if (this.path === '/api/courses' && this.method === 'GET') {
        return mockApi.getCourses();
      }
      
      if (this.path.match(/\/api\/courses\/\d+$/) && this.method === 'GET') {
        const id = this.path.match(/\/api\/courses\/(\d+)/)[1];
        return mockApi.getCourse(id);
      }
      
      if (this.path === '/api/courses' && this.method === 'POST') {
        return mockApi.createCourse(this._body, token);
      }
      
      if (this.path.match(/\/api\/courses\/\d+$/) && this.method === 'PUT') {
        const id = this.path.match(/\/api\/courses\/(\d+)/)[1];
        return mockApi.updateCourse(id, this._body, token);
      }
      
      if (this.path.match(/\/api\/courses\/\d+$/) && this.method === 'DELETE') {
        const id = this.path.match(/\/api\/courses\/(\d+)/)[1];
        return mockApi.deleteCourse(id, token);
      }
      
      if (this.path === '/api/events/upcoming' && this.method === 'GET') {
        return mockApi.getUpcomingEvents();
      }
      
      if (this.path.match(/\/api\/events\/\d+$/) && this.method === 'GET') {
        const id = this.path.match(/\/api\/events\/(\d+)/)[1];
        return mockApi.getEvent(id);
      }
      
      if (this.path === '/api/events' && this.method === 'POST') {
        return mockApi.createEvent(this._body, token);
      }
      
      // Default response for unhandled routes
      return {
        status: 404,
        json: () => ({ message: 'Not Found' })
      };
    }
  };
  
  return req;
}

// Reset the mock database to initial state
export function resetMockDb() {
  db.users = [...users];
  db.courses = [...courses];
  db.events = [...events];
  db.enrollments = [];
  db.eventRegistrations = [];
  db.tokens = {};
}

// Mock for global.request
export const mockRequest = () => mockSupertest('http://localhost:8000'); 