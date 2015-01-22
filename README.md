# ConnectedTraffic
This project was done for a school-project. The basic idea was to have a chat built with WebSockets. Somehow I ended up with something more like a Framework than a standalone app.

## WebSockets
Is a HTML5 feature. See http://en.wikipedia.org/wiki/WebSocket and the according RFC: https://tools.ietf.org/html/rfc6455.

## What is it?
It's a Framework completely written in PHP5 (OO). It provides an very easy way to build applications like chats or other livetime event-thingys. All The communication is via WebSockets. Not a single HTTP-Request.

## Current state
As the deadline of the project hasn't reached yet, there is a lot left to do. But currently I'm able to build a chatApp on top of the framework. So the basics are up and running.

## TODOs
So what exactly is left to do?

### Framework
- Feature: Build the Notifier-Concept (Server-Side events, eg via INOTIFY or Time-based (maybe redis-support?!))
- Protocol: Make it possible to remove unused header-fields.
- Protocol: Keeping performance in mind: make it possible to work without field-names to reduce data-usage
- Code: Frame-Classes need refactoring
- Bug: Large messages support (>65k chars)
- Bug: Binary-message support (File-sending/receiving)
- Exception: work with more and actively.
- Documentation of every file/class/method

### For developers/users
- Javascript Client/Library needs a lot of improvement
- examples, so everyone can get it running within seconds
- Documentation: Tutorials, HowTos, Protocol, FAQs, etc.
