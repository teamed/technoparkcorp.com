---
layout: article
date: 2014-08-08
permalink: innovations/test-planning
label: Bugs Planning
title: "Planning Of Software Bugs"
intro: "Learn how we plan software bugs to improve the overal quality of product"
description: |
  One of the most common mistake in software development is a
  negative attitute towards bugs. In the article we explain
  that software bugs should be planned the same way as you
  plan new features and releases of your product.
keywords:
  - test planning
  - software testing
  - bugs planning
  - software defects
  - software bugs
  - defective software
  - software quality
  - quality control
next: innovations/metrics
---

You won't build a quality software product if your mindset is as follows:

        \begin{itemize}
            \item I release the product when quality testers give the okay
            \item The software quality is validated by testers
            \item High quality means complete lack of bugs
        \end{itemize}

        The above statements are common for
        testers, programmers and project sponsors~\cite[pp. 17--26]{kaner99}.
        During the last 40 years, software books have told us that 
        the assumptions above are the biggest software problems themselves~\cite{brooks95}, but
        software teams still operate under these assumptions.

    \section*{Iterative Testing is Different, Upside Down}

        First, there are a number of critical axioms that you as
        a project sponsor or project manager must remember:

        \textbf{Axiom 1: Any software product has an unlimited \\
        number of bugs not discovered yet}~\cite[p. 6]{myers04}.

        In other words you can't control number of bugs
        in the software they are unlimited. You can only control the
        number of bugs you discover, which is dependant on the limit of your time and resources.

        \textbf{Axiom 2: Quality is the degree to which \\
        a set of characteristics fulfills requirements}~\cite[p. 180]{pmbok}.

        Quality shows how \textit{good} your product is compared to 
        your initial expectations. In combining these two axioms we can say
        that the software product will be ``better'' when you 
        find and fix more bugs. 
        The characteristics are the number of fixed bugs, while
        requirements are the number of bugs anticipated.
        
        Formally, Quality of Implementation (\textbf{QoI}) is a relation between
        the number of fixed bugs (\textbf{F}) and number of bugs anticipated (\textbf{P}):

        \begin{center}
        \begin{equation*}
        \displaystyle
            QoI = \frac{F}{P}
        \end{equation*}
        \end{center}

        Quality of Product (\textbf{QoP}) is a relationship between the number
        of bugs anticipated to be discovered (\textbf{P}) and the product size (\textbf{S})
        multiplied by a nominal number of bugs per size unit (\textbf{k}).

        \begin{center}
        \begin{equation*}
        \displaystyle
            QoP = \frac{P}{S \times k}
        \end{equation*}
        \end{center}

        The more bugs you anticipate you will discover, the larger 
        your testing budget, the more time you need, and the higher you Quality of Product.

        When you say ``\textit{I want to create a quality product}'' it means that
        you are planning to develop the software and to find/fix more
        bugs than usual leading to a QoP higher than usual. For example 
        the product specification will have 100 functional requirements. 
        Usually, your testers find 5 bugs per one functional requirement, 
        spending 0.5 man-hours per one bug (250 totall man-hours). 
        ``Software of higher quality'' will mean, for example, 10 bugs per one requirement, and 1000
        bugs in the entire product. This will require additional budget and may
        increase the average cost of one bug, this will honestly
        mean that you're planning to create ``a product of higher quality''.

        When you say ``\textit{the software is properly tested}'' it means that
        your testers discovered all the bugs you anticipated discovering
        and that programmers fixed the majority of them. If you anticipated
        finding 1000 bugs, and you found 900 while 100 of them remained
        unfixed, then the Quality of Implementation is 80\%.

        Now let's review the assumptions listed above.

        \textbf{I release the product when my quality testers give the okay} (wrong).
        Testers are not performing quality control, they are discovering bugs.
        Testers don't know how this process affects your
        judgment about product release. You release the product to the
        market when you think the QoI/QoP is sufficient. 

            \textbf{The software quality is validated by testers} (wrong).
        The testers's job is to discover bugs. They don't control
        the QoI or QoP --- a project manager plans Quality of Product (QoP)
        and programmers reach Quality of Implementation (QoI). Testers
        create inputs (bugs) for programmers.

            \textbf{High quality means a complete lack of bugs} (wrong).
            The more bugs you are anticipating to discover,
            the higher the Quality of Product you are going to achieve. The more bugs you  
            discover and fix, the higher the Quality of Implementation.
            When the team reports to you that there have been no bugs found, 
            you know that the project has been
            poorly planned and testing poorly performed.

    \section*{How to Estimate Number of Bugs?}

        The most common question is ``\textit{how can we estimate
        the number of bugs to be found in the product?}''
        The answers are expert judgment, historical information,
        and prototyping.

        Remember, that you may always change this number during
        the course of project. After all, we do this iteratively, don't we? You
        anticipate the number of bugs according to your own judgment. It's
        good to start with something like (this is \textbf{k} in the formula above): 
        
        \begin{itemize}
            \item bugs per line of code
            \item bugs per functional requirement
            \item bugs per class/method/module
            \item bugs per web page
        \end{itemize}

        You can create your own metrics. Your first priority is to anticipate
        the number of bugs and your second priority is to anticipate it correctly.
        
        \section*{How to Estimate Discovery Time for Bugs?}

            Do this in the same manner in which your programmers estimate their time.
            Testing is the same process as programming
            and any other engineering task. 
            
            Before the next iteration quality tester receives the SRS,
            the list of functional requirements to be tested in a particular
            iteration, the required numbers of bugs to be detected should be decided and
            a time estimate --- how much time will it take him/her to discover the required number of bugs --- to be produced.

            If the answer is ``\textit{I don't know how many bugs this
            functionality has},'' refer him/her to Axiom 1.

    \section*{Conclusion or What Do You Do Next}

        Bugs have to be anticipated the same way as you plan new 
        features and new releases. You have to plan your quality
        and control it during the entire project. Your plan may 
        fall wide of the mark in the beginning of the project, 
        you may underestimate testing efforts, 
        and your important assumptions may be incorrect.
        But the biggest mistake you could make is not to anticipate bugs at all.

    \begin{thebibliography}{99}

            \bibitem[brooks95]{brooks95}
            Brooks, Frederick P.,
            The mythical man-month: essays on software engineering,
            Addison Wesley Longman, Inc., USA (1995)

            \bibitem[kaner99]{kaner99}
            Kaner Cem, Falk Jack, Nguyen Hung Quoc,
            Testing computer software, Second Edition,
            John Wiley and Sons, Inc, New York, USA (1999)

            \bibitem[nguyen03]{nguyen03}
            Nguyen Hung Quoc, Johnson Bob, Hackett Michael,
            Testing applications on the Web, Second Edition,
            John Wiley and Sons, New York, USA (2003)
            
            \bibitem[myers04]{myers04} 
            Myers, Glenford J., 
            The art of software testing, 2nd edition,
            John Wiley and Sons, New Jersey, USA (2004)

            \bibitem[pmbok]{pmbok}
            A Guide to the Project Management Body of Knowledge (PMBOK Guide), Third Edition (2004)
            Project Management Institute (PMI),
            PMI Press, USA (2004)

    \end{thebibliography}
