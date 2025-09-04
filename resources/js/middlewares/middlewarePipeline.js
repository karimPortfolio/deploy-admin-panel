export function middlewarePipeline(context, middleware, index) {
  const nextMiddleware = middleware[index];

  if (!nextMiddleware) {
    return context.next;
  }

  return async (to = null) => {
    if (to !== null) {
      return context.next(to);
    }

    await nextMiddleware({
      ...context,
      next: middlewarePipeline(context, middleware, index + 1),
    });
  };
}