---
layout: article
date: 2014-08-08
permalink: process/scope/requirement
label: Requirement
term: specification
title: "Requirement Is An Atomic Unit Of Product Scope"
intro: "Read about Functional and Non-functional Requirements"
description: |
  Single requirement (functional and non-functional) is an
  atomic element of product scope definition.
keywords:
  - requirement
  - non-functional requirement
  - functional rquirement
  - system requirement
  - quality requirement
  - scalability
  - supportability
  - reliability
  - specification
  - interface
  - error message
  - continuous integration
  - software outsourcing
  - offshore development
  - distributed programming
next: process/scope/matrix
---

A Requirement is a type of contractual clause between the user and their system. A Requirement 
states exactly what each user can do with the system. It is important to note that a system will not 
perform any functions outside of those stated in the Requirements.

There are Functional and Non-functional Requirements. A Functional Requirement answers the question 
"what should the system do", and a Non-functional Requirement provides an answer to "how will the 
system do this"?

Functional Requirements respond when the user makes a 'Request', causing the system to create a 
'Reply'. A 'Request' is a formal interaction between the user and system through one of its external 
interfaces. A 'Reply' is the term for how the system reacts after receiving a Request from the user. 
Functional Requirements are supported with Non-functional Requirements, which are supplementary 
specifications in a [SRS](/process/scope/srs).

Examples of Functional Requirements and their supplementary specifications include the following:

<table>
        <tr>
            <th style="width: 25px;">#</th>
            <th>Requirement</th>
            <th style="width: 100px;">Supplementary</th>
        </tr>
        <tr><td>R1</td><td>User can submit a Request to change the password associated with
        their account, the system shall generate a Reply by changing the database records
        for the user.</td><td>
        Web user interface layout, Errors list</td></tr>

        <tr><td>R2</td><td>Affiliate can submit a Request for an updated report, the system shall
        generate a Reply with a sorted snapshot of all data for the last week.</td><td>
        XML API Description, Errors list</td></tr>

        <tr><td>R3</td><td>Administrator can submit a Request for a list of accounts sorted by
        the date of most recent login, the system shall generate a Reply and sorts the list accordingly.</td><td>
        Web interface layout</td></tr>
    </table>

Non-functional requirements can define the following quality characteristics of the system:

 * External Interfaces and Error Codes
 * Interface Mock-ups and Graphics
 * Availability, Reliability, Performance, etc.

Examples of Non-functional Requirements (for the classifications above) include the following:

<table>
        <tr>
            <th>#</th>
            <th>Definition</th>
            <th style="width: 100px;">Class</th>
        </tr>
        <tr><td>IF1</td><td>System provides XML-based API as defined in the Protocol Description (Protocol-v1.5.pdf) via HTTPS socket.</td><td>External Interface</td></tr>

        <tr><td>SS1</td><td>System works without crashing during 100 hours in 5 consecutive tests with an average load of 1000 concurrent users.</td><td>Reliability</td></tr>

        <tr><td>SS2</td><td>System responds to any type of report request within a 500ms timeframe on the server equipment defined in SRS, and with up to 500 concurrent users, over the course of 100 minutes.</td><td>Performance</td></tr>
    </table>

Requirements are listed in the SRS, which also includes the Glossary, Use Cases, Functional 
Requirements, and Non-Functional Requirements.
