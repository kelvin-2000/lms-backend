// Common helper functions for tests
export const mock = (impl) => {
  const fn = impl || ((...args) => fn.mock.calls.push(args));
  fn.mock = { calls: [] };
  return fn;
}; 