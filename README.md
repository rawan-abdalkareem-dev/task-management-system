# Task Management System

A simple web-based project and task management system developed as a university course project for Software Project Management.

## Project Overview

The system helps teams manage projects and tasks using a simple Kanban board. It supports project management, task assignment, sub-tasks, status tracking, activity logging, and project reporting.

## Technologies

- PHP
- MySQL
- HTML
- CSS
- JavaScript
- XAMPP
- Git & GitHub

## Main Features

- Create and manage projects.
- Create tasks inside projects.
- Set task priority: High, Medium, or Low.
- Assign tasks to team members.
- Create parent tasks and sub-tasks.
- Manage task status using a Kanban board:
  - New
  - In Progress
  - Completed
- Record task status changes in an activity log.
- Generate a simple project report including:
  - Total tasks
  - Completed tasks per member
  - Overall completion percentage

## Team

- Rawan — Project Management / Database
- Sara — Frontend / Project & Task Management
- Hala — Backend / Task Status & Assignment
- Lujain — Activity Log / Reports

## GitHub Workflow

The project follows a collaborative Git workflow:

```text
feature branch
      ↓
Pull Request
      ↓
dev
      ↓
Pull Request
      ↓
main

Each team member works on a separate feature branch. Pull Requests are used to review and merge changes.

Database

The project uses MySQL as the database system.

The database is created locally using phpMyAdmin/XAMPP. Database files and local database credentials are not uploaded to GitHub.

Running the Project Locally
Install XAMPP.
Start Apache and MySQL.
Create the project_management database using phpMyAdmin.
Configure the local database connection in db.php.
Place the project inside the XAMPP htdocs directory.
Open the project through the local Apache server.
Project Development

The project was developed across three sprints:

Sprint 1
Database Design
GitHub Setup
Project Management
Task Management
Sprint 2
Kanban Board
Task Status
Task Assignment
Sub-task Management
Activity Log
Project Report
Sprint 3
Final Testing
Code Review
Documentation
Final Submission Preparation
