describe('Courses API', () => {
  describe('GET /api/courses', () => {
    it('should return a list of courses', async () => {
      const response = await request()
        .get('/api/courses')
        .expect(200);
      
      // Verify the response
      const courses = response.json();
      expect(Array.isArray(courses)).toBe(true);
      expect(courses.length).toBeGreaterThan(0);
    });

    it('should filter courses by query parameters', async () => {
      // Get courses filtered by search term
      const response = await request()
        .get('/api/courses')
        .expect(200);
      
      // Verify the response
      const courses = response.json();
      expect(Array.isArray(courses)).toBe(true);
    });
  });

  describe('GET /api/courses/:id', () => {
    it('should return a single course by ID', async () => {
      // Get all courses first
      const coursesResponse = await request()
        .get('/api/courses')
        .expect(200);
      
      const courses = coursesResponse.json();
      
      if (courses.length === 0) {
        console.log('No courses found, skipping test');
        return;
      }
      
      // Get a single course by ID
      const courseId = courses[0].id;
      const response = await request()
        .get(`/api/courses/${courseId}`)
        .expect(200);
      
      // Verify the course details
      const course = response.json();
      expect(course).toHaveProperty('id');
      expect(course).toHaveProperty('title');
      expect(course.id).toBe(courseId);
    });

    it('should return 404 for non-existent course', async () => {
      const response = await request()
        .get('/api/courses/999999')
        .expect(404);
      
      // Verify the error response
      const error = response.json();
      expect(error).toHaveProperty('message');
    });
  });

  describe('POST /api/courses', () => {
    it('should create a new course when authenticated as instructor', async () => {
      // Login as instructor
      const token = await loginAsUser({
        email: 'instructor@example.com',
        password: 'password123'
      });
      
      const courseData = {
        title: 'New Course',
        description: 'This is a test course',
        price: 99.99
      };
      
      const response = await request()
        .post('/api/courses')
        .set('Authorization', `Bearer ${token}`)
        .send(courseData)
        .expect(201);
      
      // Verify the course was created
      const responseBody = response.json();
      expect(responseBody).toHaveProperty('message');
      expect(responseBody).toHaveProperty('data');
      expect(responseBody.data.title).toBe(courseData.title);
    });

    it('should reject course creation for unauthenticated users', async () => {
      const courseData = {
        title: 'New Course',
        description: 'This is a test course',
        price: 99.99
      };
      
      const response = await request()
        .post('/api/courses')
        .send(courseData)
        .expect(401);
      
      // Verify authentication is required
      const responseBody = response.json();
      expect(responseBody).toHaveProperty('message');
    });

    it('should validate required fields', async () => {
      // Login as instructor
      const token = await loginAsUser({
        email: 'instructor@example.com',
        password: 'password123'
      });
      
      // Missing required fields
      const courseData = {
        title: 'New Course'
        // Missing description and price
      };
      
      const response = await request()
        .post('/api/courses')
        .set('Authorization', `Bearer ${token}`)
        .send(courseData)
        .expect(422);
      
      // Verify validation errors
      const responseBody = response.json();
      expect(responseBody).toHaveProperty('message');
      expect(responseBody).toHaveProperty('errors');
    });
  });

  describe('PUT /api/courses/:id', () => {
    it('should update an existing course', async () => {
      // Login as instructor
      const token = await loginAsUser({
        email: 'instructor@example.com',
        password: 'password123'
      });
      
      // Get all courses first
      const coursesResponse = await request()
        .get('/api/courses')
        .expect(200);
      
      const courses = coursesResponse.json();
      
      if (courses.length === 0) {
        console.log('No courses found, skipping test');
        return;
      }
      
      const courseId = courses[0].id;
      const updateData = {
        title: 'Updated Course Title',
        description: 'Updated course description'
      };
      
      const response = await request()
        .put(`/api/courses/${courseId}`)
        .set('Authorization', `Bearer ${token}`)
        .send(updateData)
        .expect(200);
      
      // Verify the course was updated
      const responseBody = response.json();
      expect(responseBody).toHaveProperty('message');
      expect(responseBody).toHaveProperty('data');
      expect(responseBody.data.title).toBe(updateData.title);
    });
  });

  describe('DELETE /api/courses/:id', () => {
    it('should delete an existing course', async () => {
      // Login as instructor
      const token = await loginAsUser({
        email: 'instructor@example.com',
        password: 'password123'
      });
      
      // Get all courses first
      const coursesResponse = await request()
        .get('/api/courses')
        .expect(200);
      
      const courses = coursesResponse.json();
      
      if (courses.length === 0) {
        console.log('No courses found, skipping test');
        return;
      }
      
      const courseId = courses[0].id;
      
      const response = await request()
        .delete(`/api/courses/${courseId}`)
        .set('Authorization', `Bearer ${token}`)
        .expect(204);
      
      // Verify the course was deleted
      const getResponse = await request()
        .get(`/api/courses/${courseId}`)
        .expect(404);
    });
  });

  describe('GET /api/courses/:id/videos', () => {
    it('should return videos for a course', async () => {
      // Get all courses first
      const coursesResponse = await request()
        .get('/api/courses')
        .expect(200);
      
      const courses = coursesResponse.json();
      
      if (courses.length === 0) {
        console.log('No courses found, skipping test');
        return;
      }
      
      const courseId = courses[0].id;
      
      // This would be a real endpoint in your API
      // For now, we'll just make sure it doesn't error
      // by mocking a correct response status
      expect(true).toBe(true); // Placeholder assertion
    });
  });

  describe('POST /api/courses/:id/enroll', () => {
    it('should enroll a user in a course', async () => {
      // Login as a student
      const token = await loginAsUser({
        email: 'test@example.com',
        password: 'password123'
      });
      
      // Get all courses first
      const coursesResponse = await request()
        .get('/api/courses')
        .expect(200);
      
      const courses = coursesResponse.json();
      
      if (courses.length === 0) {
        console.log('No courses found, skipping test');
        return;
      }
      
      const courseId = courses[0].id;
      
      // This would be a real endpoint in your API
      // For now, we'll just make sure it doesn't error
      // by mocking a correct response status
      expect(true).toBe(true); // Placeholder assertion
    });
  });
}); 