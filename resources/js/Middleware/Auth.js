"use strict";
export default class Auth {

    /**
     * Auth Middleware
     * @param App
     */
    constructor(App) {
        this.app = App
    }

    /**
     * Handle Next.
     * @param request {Request}
     * @param next {Function}
     * @return {*}
     */
    async handle(request, next) {
        if (request.hasMiddleware('auth') && !request.user()){
            try {
                await this.app.make('Auth').authorize()
            } catch (e) {
                if(!request.routeIs('auth.login')){
                    return next(request.redirect(
                        this.app.make('Route').link('auth.login').withQuery({
                            redirect: request.get('to.fullPath')
                        })
                    ))
                }
            }
        }
        return next(request)
    }

    /**
     * Terminate Next.
     * @param request {Request}
     * @param next {Function}
     * @return {*}
     */
    terminate(request, next) {
        return next(request)
    }
}
