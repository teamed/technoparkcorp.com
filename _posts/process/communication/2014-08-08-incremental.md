---
layout: article
date: 2014-08-08
permalink: process/communication/incremental
label: Incremental Delivery
term: acceptancestatement
title: "Delivery Of Increments"
intro: "Read how we deliver the product in functional increments"
description: |
  Incremental delivery of software means that software product
  is specified, designed, implemented, tested and deployed in
  a number of builds. Each build has a new portion of
  functionality. All builds together constitute the project
  scope.
keywords:
  - incremental delivery
  - software builds
  - iterations
  - traceability matrix
next_step: process/quality
---

Incremental delivery of software means that software product is specified, designed, implemented,
tested and deployed in a number of builds. Each build has a new portion of functionality. All builds
together constitute the project [scope](/process/scope).

Consider the example development steps in a sample software project. Project statement is:

<pre>
System is an online database management tool where
end-users can find information about real estate
objects and post new ads.
</pre>

This is a list of [functional requirements](/process/scope/requirement) in the [SRS](/process/scope/srs):

<table>
    <tr>
        <th>#</th>
        <th>Requirement</th>
        <th>Build</th>
        <th>Status</th>
    </tr>

    <tr><td>R1</td><td>End-user can initiate search by keyword, System shall display search result</td><td>#2</td><td>accepted</td></tr>
    <tr><td>R2</td><td>End-user can post a new ad, System shall validate information and include new ad into database</td><td>#2</td><td>accepted</td></tr>
    <tr><td>R3</td><td>End-user can scroll search result in paging mode, System shall display different pages with search result</td><td>#3</td><td>accepted</td></tr>
    <tr><td>R4</td><td>Administrator can list users and block any, System shall display user list and delete selected users</td><td>#3</td><td>tested</td></tr>
    <tr><td>R4.1</td><td>Administrator can sort list of users, System shall sort according to selected sorting criteria</td><td>#3</td><td>implemented</td></tr>
</table>

This is an example of the [Schedule](/process/time/schedule) for the sample project:

<table>
    <tr>
        <th>#</th>
        <th>Objective</th>
        <th>Finish</th>
        <th>Staff-hours</th>
    </tr>
    <tr><td/><td>Project start</td><td>28-Jan</td><td/></tr>
    <tr><td>1</td><td>Inception Phase</td><td>8-Feb</td><td>120</td></tr>
    <tr><td>M</td><td>Budget Approval (LCO Milestone)</td><td>8-Feb</td><td/></tr>
    <tr><td>2</td><td>R1, R2</td><td>22-Feb</td><td>180</td></tr>
    <tr><td>3</td><td>R3, R4, R4.1</td><td>4-Mar</td><td>260</td></tr>
    <tr><td>M</td><td>Product Readiness (IOC Milestone)</td><td>18-Mar</td><td/></tr>
    <tr><td>4</td><td>Transition Phase</td><td>3-Apr</td><td>140</td></tr>
    <tr><td>M</td><td>Project finished (Release Milestone)</td><td>3-Apr</td><td/></tr>
</table>

Imagine that the project is in [Iteration](/process/time/iteration) #3 and right now the project
team is performing user acceptance testing with you for build #3.

The example illustrates that each new Iteration gives you a new workable product, and serves as an
extension to the previous build.
