---
layout: article
date: 2014-08-08
permalink: process/scope/specification
label: Specification
term: specification
title: "Top Level User Requirement"
intro: "Learn about our approaches to Specification development"
description: |
  Scope specification (either Vision or SRS) defines
  boundaries of software development product.
keywords:
  - specification
  - software requirements specification
  - SRS
  - functional requirement
  - non-functional requirement
  - quality requirement
  - continuous integration
  - software outsourcing
  - offshore development
  - distributed programming
next: process/scope/srs
---

Specifications define the boundaries of a system. A Specification is the core document in any 
software project, and is so important that its value cannot be overestimated.

The following advantages are brought to the project by a properly defined Specification:

 * Your involvement is high from early Iterations
 * [Schedule](/process/time/schedule) is accurate and the Milestones are met without delays
 * [Risks](/process/risk) are identified and managed in advance, the project is stable
 * [Budget](/process/cost/budget) includes only real numbers and is optimal
 * Team morale is high since all parties involved know the project objectives
 * [Support](/process/warranty/support) and enhancements are easy, even if the team experiences changes

<tikz>
\begin{tikzpicture}
    [node distance = 2.5cm,
    auto, thick]

    \node [cloud, draw=tpcRed, cloud ignores aspect, fill=tpcRed!40] (request) {Informal Request};

    \node [block, below of=request] (preliminary) {Preliminary Specification};
    \node [block, below of=preliminary] (alpha) {Alpha-Specification};
    \node [block, below of=alpha] (beta) {Beta-Specification};
    \node [choice, below of=beta, aspect=1.5, text width=3cm] (lco) {LCO Milestone};
    \node [block, text width=5cm, below=1.8cm of lco] (effective) {Effective Specification};

    \path [line] (request) -- node [right, text width=7cm] {System analysis of scope} (preliminary);
    \path [line] (preliminary) -- node [right, text width=7cm] {Preliminary Specification and ROM Estimate are approved by Customer} (alpha);
    \path [line] (alpha) --  node[right, text width=5cm] (comment) {There are no more comments from Customer} (beta);
    \path [line] (beta) -- (lco);
    \path [line] (lco) -- node[right, text width=7cm] {Specification, Budget, Schedule, and Risks are approved} (effective);

    \node [draw=tpcBlue, dashed, fit=(alpha) (beta) (comment)] (group) {};
    \node [below=0.3cm of group.south east, anchor=east] {Inception Phase};

\end{tikzpicture}
    </tikz>

Requirements are gathered from your informal customer. A preliminary Specification, called the 
[Vision](/process/scope/vision), is then created. Utilizing more defined details, an 
Alpha-Specification is subsequently created based upon the [Vision](/process/scope/vision). You are 
an active participant throughout this entire process. The Alpha-Specification is created during the 
[Inception Phase](/process/time/inception).

When the Alpha-Specification is ready and receives your approval, it becomes a Beta-Specification on 
a [LCO Milestone](/process/time/lco). The Beta-Specification is a paper prototype of the system, and 
is presented in the form of a [SRS](/process/scope/srs).

On the LCO Milestone, you approve the continuation of the project, and the Specification becomes effective.

The effective Specification is used by the project team. All Requirements are 
[communicated](/process/communication) with you through documented changes in the effective 
Specification. Communicating any changes ensures that you will be aware of the project status and 
the activities of the project team at all times.

The Specification can be changed at any time during project lifecycle. All changes to the effective 
Specification are implemented as new Specifications through a process called [Change 
Requests](/process/scope/changerequest}">Change Requests

<a href="${url:process/scope/changerequest)     are small documents (1-2 pages) created by a system analyst in response to concerns about the project from you     and other stakeholders."/>

.
